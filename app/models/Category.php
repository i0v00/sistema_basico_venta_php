<?php
namespace App\Models;

use Core\Database;

class Category {
    public static function all() {
        $db = Database::getConnection();
        return $db->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
    }

    public static function create($data) {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO categories (name, icon) VALUES (?, ?)");
        return $stmt->execute([
            $data['name'],
            $data['icon'] ?? '🍔'
        ]);
    }

    public static function delete($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
