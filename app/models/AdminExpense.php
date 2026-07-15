<?php
namespace App\Models;

use Core\Database;

class AdminExpense {
    public static function all($startDate = null, $endDate = null) {
        $db = Database::getConnection();
        $query = "SELECT g.*, p.nombre as producto_nombre 
                  FROM gastos_administrativos g 
                  JOIN productos_administrativos p ON g.producto_id = p.id WHERE 1=1";
        $params = [];

        if ($startDate) {
            $query .= " AND g.fecha >= ?";
            $params[] = $startDate;
        }
        if ($endDate) {
            $query .= " AND g.fecha <= ?";
            $params[] = $endDate;
        }

        $query .= " ORDER BY g.fecha DESC, g.id DESC";
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT g.*, p.nombre as producto_nombre FROM gastos_administrativos g JOIN productos_administrativos p ON g.producto_id = p.id WHERE g.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create($data) {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO gastos_administrativos (producto_id, cantidad, precio_unitario, total, fecha) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['producto_id'],
            $data['cantidad'],
            $data['precio_unitario'],
            $data['total'],
            $data['fecha']
        ]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE gastos_administrativos SET producto_id = ?, cantidad = ?, precio_unitario = ?, total = ?, fecha = ? WHERE id = ?");
        return $stmt->execute([
            $data['producto_id'],
            $data['cantidad'],
            $data['precio_unitario'],
            $data['total'],
            $data['fecha'],
            $id
        ]);
    }

    public static function delete($id) {
        $db = Database::getConnection();
        // Hard delete
        $stmt = $db->prepare("DELETE FROM gastos_administrativos WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function getTotalExpensesForRange($startDate, $endDate) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT SUM(total) as total FROM gastos_administrativos WHERE fecha >= ? AND fecha <= ?");
        $stmt->execute([$startDate, $endDate]);
        $row = $stmt->fetch();
        return (float)($row['total'] ?? 0.00);
    }
}
