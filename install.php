<?php
/**
 * Interactive Database Installer
 */

require_once __DIR__ . '/env.php';
require_once __DIR__ . '/core/Database.php';

use Core\Database;

try {
    $db = Database::getConnection();
    
    // Read the SQL script
    $sqlFile = __DIR__ . '/database/database.sql';
    if (!file_exists($sqlFile)) {
        die("Error: No se encontró el archivo database.sql en $sqlFile");
    }

    $sqlContent = file_get_contents($sqlFile);

    // Remove CREATE DATABASE and USE statements so it runs directly inside the database configured in .env
    $sqlContent = preg_replace('/CREATE DATABASE IF NOT EXISTS `[^`]+`[^;]*;/i', '', $sqlContent);
    $sqlContent = preg_replace('/USE `[^`]+`;/i', '', $sqlContent);

    // Execute queries
    $db->exec($sqlContent);

    // Re-hash the default admin password to ensure it is 100% correct and works on this server's PHP version
    $passwordHash = password_hash('admin', PASSWORD_BCRYPT);
    
    // Insert or update the admin user
    $stmt = $db->prepare("INSERT INTO users (id, username, password) VALUES (1, 'admin', ?) ON DUPLICATE KEY UPDATE password = ?");
    $stmt->execute([$passwordHash, $passwordHash]);

    // Get base path
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $basePath = dirname($scriptName);
    $basePath = ($basePath === '/' || $basePath === '\\') ? '' : $basePath;

    echo "<div style='font-family:sans-serif;padding:30px;max-width:600px;margin:50px auto;background:#FFF8F0;border:2px solid #E07B39;border-radius:16px;box-shadow:0 10px 15px -3px rgba(0,0,0,0.1); text-align:center;'>
        <span style='font-size:50px;'>🍔</span>
        <h2 style='color:#3D1C02;margin-top:10px;'>¡Base de Datos Instalada!</h2>
        <p style='color:#7B4F2E;'>Las tablas y los datos de prueba han sido instalados correctamente en la base de datos: <strong style='color:#E07B39;'>{$_ENV['DB_NAME']}</strong></p>
        <div style='background:white;padding:15px;border-radius:8px;border:1px solid #F5E6D3;margin:20px 0;text-align:left;font-size:14px;color:#3D1C02;'>
            <strong>Credenciales de acceso:</strong><br>
            • Usuario: <code style='background:#FFF8F0;padding:2px 6px;border-radius:4px;'>admin</code><br>
            • Contraseña: <code style='background:#FFF8F0;padding:2px 6px;border-radius:4px;'>admin</code>
        </div>
        <p style='font-size:12px;color:#A0714F;'>Por favor elimina el archivo <code>install.php</code> por seguridad antes de subirlo a producción.</p>
        <a href='{$basePath}/' style='display:inline-block;background:#E07B39;color:white;text-decoration:none;font-weight:bold;padding:12px 30px;border-radius:10px;margin-top:10px;transition:0.2s;'>Ir al Login 🔑</a>
    </div>";

} catch (Exception $e) {
    echo "<div style='font-family:sans-serif;padding:30px;max-width:600px;margin:50px auto;background:#FFF5F5;border:2px solid #E53E3E;border-radius:16px;box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);'>
        <span style='font-size:50px;'>⚠️</span>
        <h2 style='color:#9B2C2C;margin-top:10px;'>Error de Instalación</h2>
        <p style='color:#C53030;'>Ocurrió un error al ejecutar los comandos en la base de datos:</p>
        <pre style='background:#FFF;padding:15px;border-radius:8px;border:1px solid #FED7D7;font-size:12px;overflow-x:auto;color:#C53030;'>{$e->getMessage()}</pre>
        <p style='font-size:14px;color:#742A2A;'>Asegúrate de que la base de datos <strong>{$_ENV['DB_NAME']}</strong> existe en MySQL y que las credenciales en tu archivo <code>.env</code> sean correctas.</p>
    </div>";
}
