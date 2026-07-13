<?php
namespace Core;

use PDO;
use PDOException;

class Database {
    private static $instance = null;

    public static function getConnection() {
        if (self::$instance === null) {
            $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
            $port = $_ENV['DB_PORT'] ?? '3306';
            $db   = $_ENV['DB_NAME'] ?? 'dukes_cakes_venta';
            $user = $_ENV['DB_USER'] ?? 'root';
            $pass = $_ENV['DB_PASS'] ?? '';
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
                // Proactively run migration check for 'deleted' column
                try {
                    $stmt = self::$instance->query("SHOW COLUMNS FROM sales LIKE 'deleted'");
                    if ($stmt->rowCount() == 0) {
                        self::$instance->exec("ALTER TABLE sales ADD COLUMN deleted TINYINT(1) NOT NULL DEFAULT 0");
                    }
                } catch (\Exception $e) {
                    // Ignore schema upgrade error if table doesn't exist yet
                }
            } catch (PDOException $e) {
                // If there's an error, print a clean styled error message
                die("<div style='font-family:sans-serif;padding:20px;background:#FFF8F0;color:#3D1C02;border:1px solid #E07B39;border-radius:8px;max-width:500px;margin:50px auto;'>
                    <h3 style='color:#E07B39;'>Error de Conexión a la Base de Datos</h3>
                    <p>{$e->getMessage()}</p>
                    <small>Por favor verifica tu archivo .env y asegúrate de que el servidor MySQL esté corriendo e importado el archivo database.sql</small>
                </div>");
            }
        }
        return self::$instance;
    }
}
