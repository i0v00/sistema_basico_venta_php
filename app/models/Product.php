<?php
namespace App\Models;

use Core\Database;

class Product {
    public static function all($search = '', $categoryId = null) {
        $db = Database::getConnection();
        $query = "SELECT p.*, c.name as category_name, c.icon as category_icon 
                  FROM products p 
                  JOIN categories c ON p.category_id = c.id WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $query .= " AND (p.name LIKE ? OR p.code LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        if (!empty($categoryId)) {
            $query .= " AND p.category_id = ?";
            $params[] = $categoryId;
        }

        $query .= " ORDER BY p.name ASC";
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function getCategories() {
        $db = Database::getConnection();
        return $db->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
    }

    public static function create($data) {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO products (category_id, code, name, description, price, image, active, use_recipe) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['category_id'],
            $data['code'],
            $data['name'],
            $data['description'] ?? '',
            $data['price'],
            $data['image'] ?? null,
            $data['active'] ?? 1,
            $data['use_recipe'] ?? 0
        ]);
        $productId = (int)$db->lastInsertId();

        // Create initial base price history record (effective from 2000-01-01)
        try {
            $stmtPrice = $db->prepare("INSERT INTO product_price_history (product_id, price, effective_date) VALUES (?, ?, '2000-01-01')");
            $stmtPrice->execute([$productId, $data['price']]);
        } catch (\Exception $e) {
            // Table might not exist yet if migration hasn't run
        }

        return $productId;
    }

    public static function update($id, $data) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE products SET category_id = ?, code = ?, name = ?, description = ?, price = ?, image = ?, active = ?, use_recipe = ? WHERE id = ?");
        return $stmt->execute([
            $data['category_id'],
            $data['code'],
            $data['name'],
            $data['description'] ?? '',
            $data['price'],
            $data['image'] ?? null,
            $data['active'] ?? 1,
            $data['use_recipe'] ?? 0,
            $id
        ]);
    }

    public static function delete($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Recipe relationships
    public static function getRecipe($productId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT r.*, rm.name as material_name, rm.unit 
            FROM recipes r 
            JOIN raw_materials rm ON r.raw_material_id = rm.id 
            WHERE r.product_id = ?
        ");
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    public static function saveRecipe($productId, $materials) {
        $db = Database::getConnection();
        // Clear old recipe first
        $stmt = $db->prepare("DELETE FROM recipes WHERE product_id = ?");
        $stmt->execute([$productId]);

        if (empty($materials)) {
            return;
        }

        $stmt = $db->prepare("INSERT INTO recipes (product_id, raw_material_id, quantity) VALUES (?, ?, ?)");
        foreach ($materials as $materialId => $qty) {
            if ($qty > 0) {
                $stmt->execute([$productId, $materialId, $qty]);
            }
        }
    }

    // ── Price History ────────────────────────────────────────────────

    /**
     * Get all price history records for a product, newest first.
     */
    public static function getPriceHistory(int $productId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            "SELECT * FROM product_price_history WHERE product_id = ? ORDER BY effective_date ASC, id ASC"
        );
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    /**
     * Resolve the active price for a given date.
     * Returns the price of the most recent record whose effective_date <= $date.
     * If date is earlier than all records, uses the oldest record (base price).
     */
    public static function getPriceForDate(int $productId, string $date): float {
        $db = Database::getConnection();
        // 1. Try to find the most recent price where effective_date <= $date
        $stmt = $db->prepare(
            "SELECT price FROM product_price_history
             WHERE product_id = ? AND effective_date <= ?
             ORDER BY effective_date DESC, id DESC
             LIMIT 1"
        );
        $stmt->execute([$productId, $date]);
        $row = $stmt->fetch();

        if ($row) {
            return (float)$row['price'];
        }

        // 2. If date is earlier than all records, use the oldest recorded price (base price)
        $oldestStmt = $db->prepare(
            "SELECT price FROM product_price_history
             WHERE product_id = ?
             ORDER BY effective_date ASC, id ASC
             LIMIT 1"
        );
        $oldestStmt->execute([$productId]);
        $oldestRow = $oldestStmt->fetch();
        if ($oldestRow) {
            return (float)$oldestRow['price'];
        }

        // 3. Fallback: use current products.price
        $p = self::find($productId);
        return $p ? (float)$p['price'] : 0.0;
    }

    /**
     * Add a new price history record.
     * Also updates products.price if this becomes the most recent price,
     * and automatically syncs all existing sales for this product.
     */
    public static function addPriceHistory(int $productId, float $price, string $effectiveDate): int {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            "INSERT INTO product_price_history (product_id, price, effective_date) VALUES (?, ?, ?)"
        );
        $stmt->execute([$productId, $price, $effectiveDate]);
        $newId = (int)$db->lastInsertId();

        // Sync products.price with the most recent historical price
        self::syncCurrentPrice($productId);

        // Automatically sync all sales for this product
        self::syncAllSalesPricesForProduct($productId);

        return $newId;
    }

    /**
     * Update an existing price history record.
     * If it is the oldest/base record, its base date is preserved, but price is updated.
     */
    public static function updatePriceHistory(int $historyId, float $price, ?string $effectiveDate = null): bool {
        $db = Database::getConnection();

        // Find which product this belongs to
        $row = $db->prepare("SELECT product_id, id, effective_date FROM product_price_history WHERE id = ?");
        $row->execute([$historyId]);
        $rec = $row->fetch();
        if (!$rec) return false;

        $isOldest = self::isOldestPriceHistory($rec['product_id'], $historyId);
        $dateToSave = ($isOldest || empty($effectiveDate)) ? $rec['effective_date'] : $effectiveDate;

        $stmt = $db->prepare(
            "UPDATE product_price_history SET price = ?, effective_date = ? WHERE id = ?"
        );
        $ok = $stmt->execute([$price, $dateToSave, $historyId]);
        self::syncCurrentPrice($rec['product_id']);
        self::syncAllSalesPricesForProduct($rec['product_id']);
        return $ok;
    }

    /**
     * Delete a price history record. Cannot delete the oldest one.
     */
    public static function deletePriceHistory(int $historyId): bool {
        $db = Database::getConnection();

        $row = $db->prepare("SELECT product_id FROM product_price_history WHERE id = ?");
        $row->execute([$historyId]);
        $rec = $row->fetch();
        if (!$rec) return false;

        if (self::isOldestPriceHistory($rec['product_id'], $historyId)) {
            return false; // Cannot delete the base/oldest price
        }

        $stmt = $db->prepare("DELETE FROM product_price_history WHERE id = ?");
        $ok = $stmt->execute([$historyId]);
        self::syncCurrentPrice($rec['product_id']);
        self::syncAllSalesPricesForProduct($rec['product_id']);
        return $ok;
    }

    /**
     * Automatically update all existing sale_details and sales totals
     * for a product based on its historical prices for each sale's date.
     */
    public static function syncAllSalesPricesForProduct(int $productId): void {
        $db = Database::getConnection();
        // Get all sale_details for this product with their sale_date
        $stmt = $db->prepare("
            SELECT sd.id AS detail_id, sd.sale_id, DATE(s.sale_date) AS sale_day, sd.quantity
            FROM sale_details sd
            JOIN sales s ON sd.sale_id = s.id
            WHERE sd.product_id = ?
        ");
        $stmt->execute([$productId]);
        $details = $stmt->fetchAll();

        $affectedSaleIds = [];
        $updateDetailStmt = $db->prepare("UPDATE sale_details SET price = ? WHERE id = ?");

        foreach ($details as $d) {
            $correctPrice = self::getPriceForDate($productId, $d['sale_day']);
            $updateDetailStmt->execute([$correctPrice, $d['detail_id']]);
            $affectedSaleIds[$d['sale_id']] = true;
        }

        // Recalculate totals for all affected sales
        if (!empty($affectedSaleIds)) {
            $updateSaleStmt = $db->prepare("
                UPDATE sales s
                SET total = (SELECT COALESCE(SUM(price * quantity), 0) FROM sale_details WHERE sale_id = s.id)
                WHERE s.id = ?
            ");
            foreach (array_keys($affectedSaleIds) as $saleId) {
                $updateSaleStmt->execute([$saleId]);
            }
        }
    }

    /**
     * Check if a given history ID is the oldest record for its product.
     */
    private static function isOldestPriceHistory(int $productId, int $historyId): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            "SELECT id FROM product_price_history WHERE product_id = ? ORDER BY effective_date ASC, id ASC LIMIT 1"
        );
        $stmt->execute([$productId]);
        $oldest = $stmt->fetch();
        return $oldest && (int)$oldest['id'] === $historyId;
    }

    /**
     * Sync products.price with the most recent price in product_price_history.
     */
    private static function syncCurrentPrice(int $productId): void {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            "SELECT price FROM product_price_history WHERE product_id = ?
             ORDER BY effective_date DESC, id DESC LIMIT 1"
        );
        $stmt->execute([$productId]);
        $row = $stmt->fetch();
        if ($row) {
            $db->prepare("UPDATE products SET price = ? WHERE id = ?")->execute([$row['price'], $productId]);
        }
    }
}

