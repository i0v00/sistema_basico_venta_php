<?php
namespace App\Models;

use Core\Database;
use App\Models\RawMaterial;
use App\Models\Product;
use Exception;

class Sale {
    public static function createSale($cartItems) {
        if (empty($cartItems)) {
            throw new Exception("El carrito está vacío.");
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

            // Calculate total
            foreach ($cartItems as $item) {
                $total += $item['price'] * $item['quantity'];
                $itemsCount += $item['quantity'];
            }

            // 2. Insert into sales
            $stmt = $db->prepare("INSERT INTO sales (total, items_count) VALUES (?, ?)");
            $stmt->execute([$total, $itemsCount]);
            $saleId = $db->lastInsertId();

            // 3. Insert details and deduct materials
            $detailStmt = $db->prepare("INSERT INTO sale_details (sale_id, product_id, price, quantity) VALUES (?, ?, ?, ?)");
            foreach ($cartItems as $item) {
                $detailStmt->execute([
                    $saleId,
                    $item['id'],
                    $item['price'],
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

    public static function history($startDate = null, $endDate = null) {
        $db = Database::getConnection();
        $query = "SELECT * FROM sales WHERE 1=1";
        $params = [];

        if (!empty($startDate)) {
            $query .= " AND DATE(sale_date) >= ?";
            $params[] = $startDate;
        }
        if (!empty($endDate)) {
            $query .= " AND DATE(sale_date) <= ?";
            $params[] = $endDate;
        }

        $query .= " ORDER BY sale_date DESC";
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
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
        $day = $db->query("SELECT SUM(total) as revenue, COUNT(*) as count FROM sales WHERE DATE(sale_date) = CURDATE()")->fetch();
        // Week stats (last 7 days)
        $week = $db->query("SELECT SUM(total) as revenue, COUNT(*) as count FROM sales WHERE sale_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch();
        // Month stats (current month)
        $month = $db->query("SELECT SUM(total) as revenue, COUNT(*) as count FROM sales WHERE MONTH(sale_date) = MONTH(NOW()) AND YEAR(sale_date) = YEAR(NOW())")->fetch();

        return [
            'day_revenue' => $day['revenue'] ?? 0.00,
            'day_count' => $day['count'] ?? 0,
            'week_revenue' => $week['revenue'] ?? 0.00,
            'week_count' => $week['count'] ?? 0,
            'month_revenue' => $month['revenue'] ?? 0.00,
            'month_count' => $month['count'] ?? 0,
        ];
    }

    public static function getSalesChartData() {
        $db = Database::getConnection();
        // Last 7 days daily totals
        $stmt = $db->query("
            SELECT DATE(sale_date) as date, SUM(total) as total 
            FROM sales 
            WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
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
            GROUP BY sd.product_id 
            ORDER BY qty DESC 
            LIMIT 5
        ");
        return $stmt->fetchAll();
    }
}
