<?php
namespace App\Models;

use Core\Database;

class GastoFijo {
    public static function all($startDate = null, $endDate = null) {
        $db = Database::getConnection();
        $query = "SELECT * FROM gastos_fijos WHERE 1=1";
        $params = [];

        if ($startDate) {
            $query .= " AND fecha >= ?";
            $params[] = $startDate;
        }
        if ($endDate) {
            $query .= " AND fecha <= ?";
            $params[] = $endDate;
        }

        $query .= " ORDER BY fecha DESC, id DESC";
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM gastos_fijos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create($data) {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO gastos_fijos (nombre, precio, fecha) VALUES (?, ?, ?)");
        $stmt->execute([
            $data['nombre'],
            $data['precio'],
            $data['fecha']
        ]);
        return $db->lastInsertId();
    }

    public static function delete($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM gastos_fijos WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function getTotalForRange($startDate, $endDate) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT SUM(precio) as total FROM gastos_fijos WHERE fecha >= ? AND fecha <= ?");
        $stmt->execute([$startDate, $endDate]);
        $row = $stmt->fetch();
        return (float)($row['total'] ?? 0.00);
    }
}
