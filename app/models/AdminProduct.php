<?php
namespace App\Models;

use Core\Database;

class AdminProduct {
    public static function all($onlyActive = false) {
        $db = Database::getConnection();
        $query = "SELECT * FROM productos_administrativos";
        if ($onlyActive) {
            $query .= " WHERE activo = 1";
        }
        $query .= " ORDER BY nombre ASC";
        return $db->query($query)->fetchAll();
    }

    public static function find($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM productos_administrativos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create($data) {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO productos_administrativos (nombre, precio_unitario, activo) VALUES (?, ?, ?)");
        $stmt->execute([
            $data['nombre'],
            $data['precio_unitario'],
            $data['activo'] ?? 1
        ]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE productos_administrativos SET nombre = ?, precio_unitario = ?, activo = ? WHERE id = ?");
        return $stmt->execute([
            $data['nombre'],
            $data['precio_unitario'],
            $data['activo'] ?? 1,
            $id
        ]);
    }

    public static function delete($id) {
        $db = Database::getConnection();
        // Soft delete: set activo to 0
        $stmt = $db->prepare("UPDATE productos_administrativos SET activo = 0 WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
