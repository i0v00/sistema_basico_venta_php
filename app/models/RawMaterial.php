<?php
namespace App\Models;

use Core\Database;

class RawMaterial {
    public static function all($search = '') {
        $db = Database::getConnection();
        $query = "SELECT * FROM raw_materials";
        $params = [];

        if (!empty($search)) {
            $query .= " WHERE name LIKE ?";
            $params[] = "%$search%";
        }

        $query .= " ORDER BY name ASC";
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM raw_materials WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create($data) {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO raw_materials (name, unit, price, current_stock, min_stock) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['name'],
            $data['unit'],
            $data['price'] ?? 0.00,
            $data['current_stock'] ?? 0.00,
            $data['min_stock'] ?? 0.00
        ]);
    }

    public static function update($id, $data) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE raw_materials SET name = ?, unit = ?, price = ?, current_stock = ?, min_stock = ? WHERE id = ?");
        return $stmt->execute([
            $data['name'],
            $data['unit'],
            $data['price'] ?? 0.00,
            $data['current_stock'],
            $data['min_stock'],
            $id
        ]);
    }

    public static function delete($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM raw_materials WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Subtract raw materials based on a product sale quantity if recipe tracking is enabled.
     */
    public static function deductForProduct($productId, $qty) {
        $db = Database::getConnection();
        // Check recipe
        $stmt = $db->prepare("SELECT raw_material_id, quantity FROM recipes WHERE product_id = ?");
        $stmt->execute([$productId]);
        $ingredients = $stmt->fetchAll();

        $updateStmt = $db->prepare("UPDATE raw_materials SET current_stock = current_stock - ? WHERE id = ?");
        foreach ($ingredients as $ing) {
            $deduction = $ing['quantity'] * $qty;
            $updateStmt->execute([$deduction, $ing['raw_material_id']]);
        }
    }

    /**
     * Check if there is enough stock for a recipe before making a sale.
     * Returns array of errors if insufficient, or empty array if ok.
     */
    public static function checkStockAvailability($productId, $qty) {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT r.quantity as required_qty, rm.name, rm.current_stock, rm.unit 
            FROM recipes r 
            JOIN raw_materials rm ON r.raw_material_id = rm.id 
            WHERE r.product_id = ?
        ");
        $stmt->execute([$productId]);
        $ingredients = $stmt->fetchAll();

        $errors = [];
        foreach ($ingredients as $ing) {
            $totalRequired = $ing['required_qty'] * $qty;
            if ($ing['current_stock'] < $totalRequired) {
                $errors[] = "Stock insuficiente de {$ing['name']}: se necesitan " . number_format($totalRequired, 2) . " {$ing['unit']} pero solo hay " . number_format($ing['current_stock'], 2) . " {$ing['unit']}.";
            }
        }
        return $errors;
    }
}
