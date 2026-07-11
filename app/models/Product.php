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
        return $db->lastInsertId();
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
}
