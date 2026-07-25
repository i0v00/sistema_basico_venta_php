<?php
namespace App\Models;

use Core\Database;

class Compra {
    public static function all($startDate = null, $endDate = null) {
        $db = Database::getConnection();
        $query = "SELECT c.*, rm.name as material_name, rm.unit 
                  FROM compras c 
                  JOIN raw_materials rm ON c.raw_material_id = rm.id 
                  WHERE 1=1";
        $params = [];

        if ($startDate) {
            $query .= " AND c.fecha >= ?";
            $params[] = $startDate;
        }
        if ($endDate) {
            $query .= " AND c.fecha <= ?";
            $params[] = $endDate;
        }

        $query .= " ORDER BY c.fecha DESC, c.id DESC";
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find($id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT c.*, rm.name as material_name, rm.unit FROM compras c JOIN raw_materials rm ON c.raw_material_id = rm.id WHERE c.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function create($data) {
        $db = Database::getConnection();
        
        $stmt = $db->prepare("INSERT INTO compras (raw_material_id, cantidad, precio_unitario, total, fecha) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['raw_material_id'],
            $data['cantidad'],
            $data['precio_unitario'],
            $data['total'],
            $data['fecha']
        ]);
        $compraId = $db->lastInsertId();

        // Increment current stock in raw_materials and update price
        $updateStock = $db->prepare("UPDATE raw_materials SET current_stock = current_stock + ?, price = ? WHERE id = ?");
        $updateStock->execute([
            $data['cantidad'],
            $data['precio_unitario'],
            $data['raw_material_id']
        ]);

        return $compraId;
    }

    public static function delete($id) {
        $db = Database::getConnection();
        $compra = self::find($id);
        if ($compra) {
            // Deduct stock that was added by this purchase
            $updateStock = $db->prepare("UPDATE raw_materials SET current_stock = GREATEST(0, current_stock - ?) WHERE id = ?");
            $updateStock->execute([$compra['cantidad'], $compra['raw_material_id']]);

            $stmt = $db->prepare("DELETE FROM compras WHERE id = ?");
            return $stmt->execute([$id]);
        }
        return false;
    }

    public static function getTotalForRange($startDate, $endDate) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT SUM(total) as total FROM compras WHERE fecha >= ? AND fecha <= ?");
        $stmt->execute([$startDate, $endDate]);
        $row = $stmt->fetch();
        return (float)($row['total'] ?? 0.00);
    }
}
