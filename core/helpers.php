<?php
/**
 * Global Helper Functions
 */

use Core\Database;

if (!function_exists('redirect')) {
    function redirect($url) {
        if (defined('BASE_URL') && BASE_URL && strpos($url, BASE_URL) !== 0) {
            $url = BASE_URL . '/' . ltrim($url, '/');
        }
        header("Location: $url");
        exit;
    }
}

if (!function_exists('e')) {
    function e($text) {
        return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('formatMoney')) {
    function formatMoney($amount) {
        return 'Bs. ' . number_format((float)$amount, 2, '.', ',');
    }
}

if (!function_exists('view')) {
    function view($name, $data = []) {
        extract($data);
        $viewFile = __DIR__ . '/../app/views/' . $name . '.php';
        
        if (file_exists($viewFile)) {
            // Start output buffering to capture the view content
            ob_start();
            include $viewFile;
            $content = ob_get_clean();

            // Load view inside layout unless it is the login page (which handles its own or uses compact layout)
            if ($name === 'auth/login') {
                include __DIR__ . '/../app/views/layout.php'; // Will render layout but with no navbar or custom styling
            } else {
                include __DIR__ . '/../app/views/layout.php';
            }
        } else {
            die("Vista [$name] no encontrada en: $viewFile");
        }
    }
}

if (!function_exists('viewRaw')) {
    /**
     * Render a view file directly, WITHOUT the layout wrapper.
     * Used for standalone/printable pages.
     */
    function viewRaw($name, $data = []) {
        extract($data);
        $viewFile = __DIR__ . '/../app/views/' . $name . '.php';
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            die("Vista [$name] no encontrada en: $viewFile");
        }
    }
}

if (!function_exists('getSetting')) {
    function getSetting($key, $default = null) {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
            $stmt->execute([$key]);
            $result = $stmt->fetch();
            return $result ? $result['setting_value'] : $default;
        } catch (\Exception $e) {
            return $default;
        }
    }
}

if (!function_exists('setSetting')) {
    function setSetting($key, $value) {
        $db = Database::getConnection();
        $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$key, $value, $value]);
    }
}

if (!function_exists('setFlash')) {
    function setFlash($key, $message) {
        \Core\Auth::setFlash($key, $message);
    }
}

if (!function_exists('getFlash')) {
    function getFlash($key) {
        return \Core\Auth::getFlash($key);
    }
}
