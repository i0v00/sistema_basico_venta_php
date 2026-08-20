<?php
namespace App\Models;

use Core\Database;
use App\Models\RawMaterial;
use App\Models\Product;
use Exception;

class Sale {
    public static function createSale($cartItems, int $cashierId = 0, ?string $saleDate = null, string $status = 'pendiente', ?string $paymentMethod = null) {
        if (empty($cartItems)) {
            throw new Exception("El carrito está vacío.");
        }

        if (empty($paymentMethod) || !in_array($paymentMethod, ['efectivo', 'qr'], true)) {
            throw new Exception("Debes elegir un método de pago obligatorio (Efectivo o QR).");
        }

        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $total = 0;
            $itemsCount = 0;
            $trackMaterials = (getSetting('track_raw_materials', '0') === '1');

            // 1. First validate stock for all items if tracking is enabled
            if ($trackMaterials) {
                foreach ($cartItems as $item) {
                    $product = Product::find($item['id']);
                    if ($product && $product['use_recipe']) {
                        $stockErrors = RawMaterial::checkStockAvailability($item['id'], $item['quantity']);
                        if (!empty($stockErrors)) {
                            throw new Exception(implode(" | ", $stockErrors));
                        }
                    }
                }
            }

            // Determine the sale date string for price resolution
            $saleDateForPrice = $saleDate ? date('Y-m-d', strtotime($saleDate)) : date('Y-m-d');

            // Calculate total using historically resolved prices
            foreach ($cartItems as &$item) {
                // Resolve the correct historical price for the sale date
                $item['price'] = Product::getPriceForDate((int)$item['id'], $saleDateForPrice);
                $total += $item['price'] * $item['quantity'];
                $itemsCount += $item['quantity'];
            }
            unset($item);

            // 2. Insert into sales
            if ($saleDate) {
                $stmt = $db->prepare(
                    "INSERT INTO sales (total, items_count, cashier_id, status, payment_method, sale_date) VALUES (?, ?, ?, ?, ?, ?)"
                );
                $stmt->execute([$total, $itemsCount, $cashierId ?: null, $status, $paymentMethod, $saleDate]);
            } else {
                $stmt = $db->prepare(
                    "INSERT INTO sales (total, items_count, cashier_id, status, payment_method) VALUES (?, ?, ?, ?, ?)"
                );
                $stmt->execute([$total, $itemsCount, $cashierId ?: null, $status, $paymentMethod]);
            }
            $saleId = $db->lastInsertId();

            // 3. Insert details and deduct materials
            $detailStmt = $db->prepare(
                "INSERT INTO sale_details (sale_id, product_id, price, quantity) VALUES (?, ?, ?, ?)"
            );
            foreach ($cartItems as $item) {
                $detailStmt->execute([
                    $saleId,
                    $item['id'],
                    $item['price'],   // historically resolved price
                    $item['quantity']
                ]);

                // Deduct stock if enabled
                if ($trackMaterials) {
                    $product = Product::find($item['id']);
                    if ($product && $product['use_recipe']) {
                        RawMaterial::deductForProduct($item['id'], $item['quantity']);
                    }
                }
            }

            $db->commit();
            return $saleId;

        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Get active orders for the orders screen.
     * Returns today's orders with items and cashier info.
     * Optionally filter by status.
     */
    public static function getActiveOrders(?string $status = null): array {
        $db = Database::getConnection();

        $where = "WHERE DATE(s.sale_date) = CURDATE() AND s.deleted = 0";
        $params = [];

        if ($status && in_array($status, ['pendiente', 'entregado', 'finalizado'], true)) {
            $where .= " AND s.status = ?";
            $params[] = $status;
        }

        $stmt = $db->prepare("
            SELECT 
                s.id,
                s.sale_date,
                s.total,
                s.items_count,
                s.status,
                u.username  AS cashier_username,
                u.full_name AS cashier_name
            FROM sales s
            LEFT JOIN users u ON s.cashier_id = u.id
            {$where}
            ORDER BY s.sale_date DESC
        ");
        $stmt->execute($params);
        $orders = $stmt->fetchAll();

        // Attach items to each order
        if (!empty($orders)) {
            $ids = array_column($orders, 'id');
            $placeholders = implode(',', array_fill(0, count($ids), '?'));

            $itemStmt = $db->prepare("
                SELECT sd.sale_id, p.name AS product_name, c.icon AS product_icon,
                       sd.quantity, sd.price
                FROM sale_details sd
                JOIN products p ON sd.product_id = p.id
                JOIN categories c ON p.category_id = c.id
                WHERE sd.sale_id IN ({$placeholders})
                ORDER BY sd.id ASC
            ");
            $itemStmt->execute($ids);
            $allItems = $itemStmt->fetchAll();

            // Group items by sale_id
            $itemsMap = [];
            foreach ($allItems as $item) {
                $itemsMap[$item['sale_id']][] = $item;
            }

            foreach ($orders as &$order) {
                $order['items'] = $itemsMap[$order['id']] ?? [];
            }
            unset($order);
        }

        return $orders;
    }

    /**
     * Update the status of an order.
     */
    public static function updateStatus(int $saleId, string $status): bool {
        if (!in_array($status, ['pendiente', 'entregado', 'finalizado'], true)) return false;
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE sales SET status = ? WHERE id = ?");
        $stmt->execute([$status, $saleId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Logical deletion of a sale, restoring stock if needed.
     */
    public static function delete(int $saleId): bool {
        $db = Database::getConnection();
        $db->beginTransaction();
        try {
            // Get current sale state
            $stmt = $db->prepare("SELECT deleted, total FROM sales WHERE id = ?");
            $stmt->execute([$saleId]);
            $sale = $stmt->fetch();
            if (!$sale || (int)$sale['deleted'] === 1) {
                $db->rollBack();
                return false;
            }

            // Mark as deleted logically
            $stmt = $db->prepare("UPDATE sales SET deleted = 1 WHERE id = ?");
            $stmt->execute([$saleId]);

            // Restore inventory if track_raw_materials is enabled
            $trackMaterials = (getSetting('track_raw_materials', '0') === '1');
            if ($trackMaterials) {
                $details = self::getDetails($saleId);
                foreach ($details as $item) {
                    $prodStmt = $db->prepare("SELECT use_recipe FROM products WHERE id = ?");
                    $prodStmt->execute([$item['product_id']]);
                    $prod = $prodStmt->fetch();
                    if ($prod && $prod['use_recipe']) {
                        // Restore raw materials
                        $recipeStmt = $db->prepare("SELECT raw_material_id, quantity FROM recipes WHERE product_id = ?");
                        $recipeStmt->execute([$item['product_id']]);
                        $recipes = $recipeStmt->fetchAll();
                        foreach ($recipes as $recipe) {
                            $restoreQty = $recipe['quantity'] * $item['quantity'];
                            $updateRm = $db->prepare("UPDATE raw_materials SET current_stock = current_stock + ? WHERE id = ?");
                            $updateRm->execute([$restoreQty, $recipe['raw_material_id']]);
                        }
                    }
                }
            }

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }

    public static function history($startDate = null, $endDate = null) {
        $db = Database::getConnection();
        $query = "SELECT s.*, u.username AS cashier_username FROM sales s LEFT JOIN users u ON s.cashier_id = u.id WHERE s.deleted = 0";
        $params = [];

        if (!empty($startDate)) {
            $query .= " AND DATE(s.sale_date) >= ?";
            $params[] = $startDate;
        }
        if (!empty($endDate)) {
            $query .= " AND DATE(s.sale_date) <= ?";
            $params[] = $endDate;
        }

        $query .= " ORDER BY s.id DESC";
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function findById(int $saleId): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM sales WHERE id = ?");
        $stmt->execute([$saleId]);
        $sale = $stmt->fetch();
        return $sale ?: null;
    }

    public static function getDetails($saleId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT sd.*, p.name as product_name, c.icon as product_icon 
            FROM sale_details sd 
            JOIN products p ON sd.product_id = p.id 
            JOIN categories c ON p.category_id = c.id
            WHERE sd.sale_id = ?
        ");
        $stmt->execute([$saleId]);
        return $stmt->fetchAll();
    }

    public static function getStats() {
        $db = Database::getConnection();
        
        // Day stats
        $day = $db->query("SELECT SUM(total) as revenue, COUNT(*) as count FROM sales WHERE DATE(sale_date) = CURDATE() AND deleted = 0")->fetch();
        // Week stats (last 7 days)
        $week = $db->query("SELECT SUM(total) as revenue, COUNT(*) as count FROM sales WHERE sale_date >= DATE_SUB(NOW(), INTERVAL 7 DAY) AND deleted = 0")->fetch();
        // Month stats (current month)
        $month = $db->query("SELECT SUM(total) as revenue, COUNT(*) as count FROM sales WHERE MONTH(sale_date) = MONTH(NOW()) AND YEAR(sale_date) = YEAR(NOW()) AND deleted = 0")->fetch();

        return [
            'day_revenue'   => $day['revenue'] ?? 0.00,
            'day_count'     => $day['count'] ?? 0,
            'week_revenue'  => $week['revenue'] ?? 0.00,
            'week_count'    => $week['count'] ?? 0,
            'month_revenue' => $month['revenue'] ?? 0.00,
            'month_count'   => $month['count'] ?? 0,
        ];
    }

    public static function getSalesChartData() {
        $db = Database::getConnection();
        // Last 7 days daily totals
        $stmt = $db->query("
            SELECT DATE(sale_date) as date, SUM(total) as total 
            FROM sales 
            WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) AND deleted = 0
            GROUP BY DATE(sale_date) 
            ORDER BY DATE(sale_date) ASC
        ");
        return $stmt->fetchAll();
    }

    public static function getTopProducts() {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT p.name, SUM(sd.quantity) as qty 
            FROM sale_details sd 
            JOIN products p ON sd.product_id = p.id 
            JOIN sales s ON sd.sale_id = s.id
            WHERE s.deleted = 0
            GROUP BY sd.product_id 
            ORDER BY qty DESC 
            LIMIT 5
        ");
        return $stmt->fetchAll();
    }

    public static function getStatsForDate(string $date): array {
        $db = Database::getConnection();
        
        // Day stats
        $dayStmt = $db->prepare("SELECT SUM(total) as revenue, COUNT(*) as count FROM sales WHERE DATE(sale_date) = ? AND deleted = 0");
        $dayStmt->execute([$date]);
        $day = $dayStmt->fetch();

        // Week stats (last 7 days from selected date)
        $weekStmt = $db->prepare("SELECT SUM(total) as revenue, COUNT(*) as count FROM sales WHERE DATE(sale_date) >= DATE_SUB(?, INTERVAL 6 DAY) AND DATE(sale_date) <= ? AND deleted = 0");
        $weekStmt->execute([$date, $date]);
        $week = $weekStmt->fetch();

        // Month stats (month of selected date)
        $monthStmt = $db->prepare("SELECT SUM(total) as revenue, COUNT(*) as count FROM sales WHERE MONTH(sale_date) = MONTH(?) AND YEAR(sale_date) = YEAR(?) AND deleted = 0");
        $monthStmt->execute([$date, $date]);
        $month = $monthStmt->fetch();

        return [
            'day_revenue'   => $day['revenue'] ?? 0.00,
            'day_count'     => $day['count'] ?? 0,
            'week_revenue'  => $week['revenue'] ?? 0.00,
            'week_count'    => $week['count'] ?? 0,
            'month_revenue' => $month['revenue'] ?? 0.00,
            'month_count'   => $month['count'] ?? 0,
        ];
    }

    public static function getSalesChartDataForDate(string $date): array {
        $db = Database::getConnection();
        // Last 7 days daily totals relative to selected date
        $stmt = $db->prepare("
            SELECT DATE(sale_date) as date, SUM(total) as total 
            FROM sales 
            WHERE DATE(sale_date) >= DATE_SUB(?, INTERVAL 6 DAY) AND DATE(sale_date) <= ? AND deleted = 0
            GROUP BY DATE(sale_date) 
            ORDER BY DATE(sale_date) ASC
        ");
        $stmt->execute([$date, $date]);
        return $stmt->fetchAll();
    }

    public static function getTopProductsForDate(string $date): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT p.name, SUM(sd.quantity) as qty 
            FROM sale_details sd 
            JOIN products p ON sd.product_id = p.id 
            JOIN sales s ON sd.sale_id = s.id
            WHERE s.deleted = 0 AND DATE(s.sale_date) = ?
            GROUP BY sd.product_id 
            ORDER BY qty DESC 
            LIMIT 5
        ");
        $stmt->execute([$date]);
        return $stmt->fetchAll();
    }

    /**
     * Get full sales detail for a specific date (for the daily report).
     * Returns each sale with its items, cashier name, time and total.
     */
    public static function getDailySalesDetail(string $date): array {
        $db = Database::getConnection();

        // Get all sales for that day
        $salesStmt = $db->prepare("
            SELECT s.id, s.total, s.items_count, s.status, s.sale_date,
                   u.full_name as cashier_name
            FROM sales s
            LEFT JOIN users u ON s.cashier_id = u.id
            WHERE DATE(s.sale_date) = ? AND s.deleted = 0
            ORDER BY s.sale_date ASC
        ");
        $salesStmt->execute([$date]);
        $sales = $salesStmt->fetchAll();

        // For each sale, get its details
        foreach ($sales as &$sale) {
            $detailStmt = $db->prepare("
                SELECT sd.quantity, sd.price, p.name as product_name
                FROM sale_details sd
                JOIN products p ON sd.product_id = p.id
                WHERE sd.sale_id = ?
                ORDER BY p.name ASC
            ");
            $detailStmt->execute([$sale['id']]);
            $sale['items'] = $detailStmt->fetchAll();
        }
        return $sales;
    }

    public static function updatePaymentMethod(int $saleId, string $paymentMethod): bool {
        if (!in_array($paymentMethod, ['efectivo', 'qr'], true)) return false;
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE sales SET payment_method = ? WHERE id = ?");
        return $stmt->execute([$paymentMethod, $saleId]);
    }

    public static function getTotalRevenueForRange($startDate, $endDate) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT SUM(total) as total FROM sales WHERE DATE(sale_date) >= ? AND DATE(sale_date) <= ? AND deleted = 0");
        $stmt->execute([$startDate, $endDate]);
        $row = $stmt->fetch();
        return (float)($row['total'] ?? 0.00);
    }

    public static function getRevenueByPaymentMethodForRange($startDate, $endDate) {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT payment_method, SUM(total) as total 
            FROM sales 
            WHERE DATE(sale_date) >= ? AND DATE(sale_date) <= ? AND deleted = 0 
            GROUP BY payment_method
        ");
        $stmt->execute([$startDate, $endDate]);
        $rows = $stmt->fetchAll();

        $result = ['efectivo' => 0.00, 'qr' => 0.00, 'sin_especificar' => 0.00];
        foreach ($rows as $row) {
            $pm = strtolower($row['payment_method'] ?? '');
            if (isset($result[$pm])) {
                $result[$pm] = (float)$row['total'];
            } else {
                $result['sin_especificar'] += (float)$row['total'];
            }
        }
        return $result;
    }

    /**
     * Get sales revenue grouped by product category for a date range.
     * Only returns categories that had at least 1 sale.
     */
    public static function getRevenueByCategory(string $startDate, string $endDate): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT 
                c.id          AS category_id,
                c.name        AS category_name,
                c.icon        AS category_icon,
                SUM(sd.price * sd.quantity) AS total_revenue,
                SUM(sd.quantity)             AS items_sold
            FROM sale_details sd
            JOIN products p  ON sd.product_id = p.id
            JOIN categories c ON p.category_id = c.id
            JOIN sales s      ON sd.sale_id = s.id
            WHERE DATE(s.sale_date) >= ? AND DATE(s.sale_date) <= ? AND s.deleted = 0
            GROUP BY c.id, c.name, c.icon
            ORDER BY total_revenue DESC
        ");
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll();
    }

    /**
     * Export history data for CSV/Excel download (enriched rows).
     */
    public static function exportHistoryData(string $startDate, string $endDate): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT 
                s.id,
                s.sale_date,
                s.payment_method,
                s.items_count,
                s.total,
                s.status,
                u.full_name AS cashier_name,
                GROUP_CONCAT(CONCAT(p.name, ' x', sd.quantity) ORDER BY p.name SEPARATOR ' | ') AS items_detail
            FROM sales s
            LEFT JOIN users u ON s.cashier_id = u.id
            LEFT JOIN sale_details sd ON sd.sale_id = s.id
            LEFT JOIN products p ON sd.product_id = p.id
            WHERE DATE(s.sale_date) >= ? AND DATE(s.sale_date) <= ? AND s.deleted = 0
            GROUP BY s.id, s.sale_date, s.payment_method, s.items_count, s.total, s.status, u.full_name
            ORDER BY s.sale_date DESC
        ");
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll();
    }

    /**
     * Ensure all sales in the given range have their prices accurately matched
     * against the product_price_history table before generating reports.
     */
    public static function syncAllSalesPricesForRange(string $startDate, string $endDate): void {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT DISTINCT sd.product_id
            FROM sale_details sd
            JOIN sales s ON sd.sale_id = s.id
            WHERE DATE(s.sale_date) >= ? AND DATE(s.sale_date) <= ? AND s.deleted = 0
        ");
        $stmt->execute([$startDate, $endDate]);
        $productIds = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($productIds as $pid) {
            Product::syncAllSalesPricesForProduct((int)$pid);
        }
    }

    /**
     * For the reports section: group sales by product and price,
     * showing the date ranges in which each price was applied.
     * Only returns products that had more than one distinct price in the period.
     */
    public static function getProductPriceRangeReport(string $startDate, string $endDate): array {
        $db = Database::getConnection();

        // 1. Ensure all sales prices in range are synchronized with history
        self::syncAllSalesPricesForRange($startDate, $endDate);

        // 2. Query all sale_details in range
        $stmt = $db->prepare("
            SELECT
                p.id            AS product_id,
                p.name          AS product_name,
                c.icon          AS product_icon,
                sd.price        AS price,
                DATE(s.sale_date) AS sale_day,
                sd.quantity     AS quantity
            FROM sale_details sd
            JOIN sales s    ON sd.sale_id = s.id
            JOIN products p ON sd.product_id = p.id
            JOIN categories c ON p.category_id = c.id
            WHERE DATE(s.sale_date) >= ? AND DATE(s.sale_date) <= ? AND s.deleted = 0
            ORDER BY p.name ASC, DATE(s.sale_date) ASC
        ");
        $stmt->execute([$startDate, $endDate]);
        $rows = $stmt->fetchAll();

        // Group by product
        $byProduct = [];
        foreach ($rows as $r) {
            $pid   = (int)$r['product_id'];
            $price = number_format((float)$r['price'], 2, '.', '');

            if (!isset($byProduct[$pid])) {
                $byProduct[$pid] = [
                    'product_id'   => $pid,
                    'product_name' => $r['product_name'],
                    'product_icon' => $r['product_icon'],
                    'price_stats'  => []
                ];
            }
            if (!isset($byProduct[$pid]['price_stats'][$price])) {
                $byProduct[$pid]['price_stats'][$price] = [
                    'price'     => (float)$r['price'],
                    'qty'       => 0,
                    'total'     => 0.0,
                    'min_sale'  => $r['sale_day'],
                    'max_sale'  => $r['sale_day']
                ];
            }
            $byProduct[$pid]['price_stats'][$price]['qty']   += (int)$r['quantity'];
            $byProduct[$pid]['price_stats'][$price]['total'] += (float)$r['price'] * (int)$r['quantity'];
            if ($r['sale_day'] < $byProduct[$pid]['price_stats'][$price]['min_sale']) {
                $byProduct[$pid]['price_stats'][$price]['min_sale'] = $r['sale_day'];
            }
            if ($r['sale_day'] > $byProduct[$pid]['price_stats'][$price]['max_sale']) {
                $byProduct[$pid]['price_stats'][$price]['max_sale'] = $r['sale_day'];
            }
        }

        $result = [];
        foreach ($byProduct as $pid => $prod) {
            // Only show products that have 2 or more distinct prices in this period
            if (count($prod['price_stats']) < 2) {
                continue;
            }

            $history = Product::getPriceHistory($pid);
            $priceTiers = [];

            // Iterate over each distinct price charged in this period (ensures NO duplicate rows)
            foreach ($prod['price_stats'] as $priceKey => $stat) {
                $targetPrice = (float)$stat['price'];

                // Find earliest history record with this price
                $matchedHist = null;
                $nextDiffHist = null;

                for ($i = 0; $i < count($history); $i++) {
                    if (abs((float)$history[$i]['price'] - $targetPrice) < 0.001) {
                        if ($matchedHist === null) {
                            $matchedHist = $history[$i];
                        }
                    } elseif ($matchedHist !== null && $nextDiffHist === null) {
                        $nextDiffHist = $history[$i];
                        break;
                    }
                }

                // Compute Vigencia Desde
                if ($matchedHist && $matchedHist['effective_date'] > '2000-01-01' && $matchedHist['effective_date'] > $startDate) {
                    $vDesde = $matchedHist['effective_date'];
                } else {
                    $vDesde = $startDate;
                }

                // Compute Vigencia Hasta
                if ($nextDiffHist && $nextDiffHist['effective_date'] <= $endDate && $nextDiffHist['effective_date'] > '2000-01-01') {
                    $d = new \DateTime($nextDiffHist['effective_date']);
                    $d->modify('-1 day');
                    $vHasta = $d->format('Y-m-d');
                } else {
                    $vHasta = $endDate;
                }

                if ($vHasta < $vDesde) {
                    $vHasta = $endDate;
                }

                $priceTiers[] = [
                    'price'          => $stat['price'],
                    'vigencia_desde' => $vDesde,
                    'vigencia_hasta' => $vHasta,
                    'date_from'      => $stat['min_sale'],
                    'date_to'        => $stat['max_sale'],
                    'qty'            => $stat['qty'],
                    'total'          => $stat['total']
                ];
            }

            usort($priceTiers, fn($a, $b) => strcmp($a['vigencia_desde'], $b['vigencia_desde']));
            $prod['prices'] = $priceTiers;
            unset($prod['price_stats']);
            $result[] = $prod;
        }

        return $result;
    }
}

