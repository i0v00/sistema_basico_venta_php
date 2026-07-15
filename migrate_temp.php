<?php
require_once __DIR__ . '/env.php';
require_once __DIR__ . '/core/Database.php';

use Core\Database;

try {
    $db = Database::getConnection();
    
    $sql = "
    CREATE TABLE IF NOT EXISTS `productos_administrativos` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `nombre` VARCHAR(100) NOT NULL,
      `precio_unitario` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
      `activo` TINYINT(1) NOT NULL DEFAULT 1,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    CREATE TABLE IF NOT EXISTS `gastos_administrativos` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `producto_id` INT NOT NULL,
      `cantidad` INT NOT NULL DEFAULT 1,
      `precio_unitario` DECIMAL(10,2) NOT NULL,
      `total` DECIMAL(10,2) NOT NULL,
      `fecha` DATE NOT NULL,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (`producto_id`) REFERENCES `productos_administrativos` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    $db->exec($sql);
    echo "Tablas creadas exitosamente!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
