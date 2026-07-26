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

    public static function update(int $id, array $data): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE categories SET name = ?, icon = ? WHERE id = ?");
        return $stmt->execute([$data['name'], $data['icon'], $id]);
    }

    public static function find(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function delete($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
