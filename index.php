<?php
/**
 * Restaurant POS - Entry Point & Router (Root Level)
 */

// ── DEBUG TEMPORAL: mostrar errores en lugar de 500 ──
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// 1. PSR-4 Autoloader
spl_autoload_register(function ($class) {
    $base_dir = __DIR__ . '/';

    // Translate namespaces to file paths
    if (strpos($class, 'App\\') === 0) {
        $file = $base_dir . 'app/' . str_replace('\\', '/', substr($class, 4)) . '.php';
    } elseif (strpos($class, 'Core\\') === 0) {
        $file = $base_dir . 'core/' . str_replace('\\', '/', substr($class, 5)) . '.php';
    } else {
        $file = $base_dir . str_replace('\\', '/', $class) . '.php';
    }

    if (file_exists($file)) {
        require $file;
    }
});

// 2. Load Environment Variables & Helpers
require_once __DIR__ . '/env.php';
require_once __DIR__ . '/core/helpers.php';

// 3. Request Path parsing & Subfolder detection (must happen before session/auth)
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Detect if we are running in a subdirectory (e.g. /dukes_cakes_venta/)
$scriptName = $_SERVER['SCRIPT_NAME']; // E.g. /dukes_cakes_venta/index.php
$basePath = dirname($scriptName); // E.g. /dukes_cakes_venta
$basePath = ($basePath === '/' || $basePath === '\\') ? '' : $basePath;

// Prefer $_GET['url'] (passed by .htaccess on InfinityFree: index.php?url=$1)
// Fall back to REQUEST_URI (used on local/Laragon)
if (isset($_GET['url'])) {
    $requestUri = '/' . trim($_GET['url'], '/');
} else {
    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    // Strip base path from request URI to match routes correctly
    if ($basePath && strpos($requestUri, $basePath) === 0) {
        $requestUri = substr($requestUri, strlen($basePath));
    }
    $requestUri = '/' . trim($requestUri, '/');
}

// Global base URL definition for assets (must be defined before Auth::initSession)
define('BASE_URL', $basePath);

// 4. Initialize Session (after BASE_URL is defined so redirect() works)
use Core\Auth;
Auth::initSession();

// Simple Routing Map
$routes = [
    'GET' => [
        '/' => [\App\Controllers\DashboardController::class, 'index'],
        '/login' => [\App\Controllers\AuthController::class, 'showLogin'],
        '/logout' => [\App\Controllers\AuthController::class, 'logout'],
        '/products' => [\App\Controllers\ProductController::class, 'index'],
        '/products/create' => [\App\Controllers\ProductController::class, 'form'],
        '/products/edit' => [\App\Controllers\ProductController::class, 'form'],
        '/categories' => [\App\Controllers\CategoryController::class, 'index'],
        '/raw-materials' => [\App\Controllers\RawMaterialController::class, 'index'],
        '/raw-materials/create' => [\App\Controllers\RawMaterialController::class, 'form'],
        '/raw-materials/edit' => [\App\Controllers\RawMaterialController::class, 'form'],
        '/pos' => [\App\Controllers\SaleController::class, 'pos'],
        '/sales/history' => [\App\Controllers\SaleController::class, 'history'],
        '/sales/details' => [\App\Controllers\SaleController::class, 'details'],
        '/settings' => [\App\Controllers\SettingsController::class, 'index'],
    ],
    'POST' => [
        '/login' => [\App\Controllers\AuthController::class, 'login'],
        '/products/save' => [\App\Controllers\ProductController::class, 'save'],
        '/products/delete' => [\App\Controllers\ProductController::class, 'delete'],
        '/categories/save' => [\App\Controllers\CategoryController::class, 'save'],
        '/categories/delete' => [\App\Controllers\CategoryController::class, 'delete'],
        '/raw-materials/save' => [\App\Controllers\RawMaterialController::class, 'save'],
        '/raw-materials/delete' => [\App\Controllers\RawMaterialController::class, 'delete'],
        '/pos/checkout' => [\App\Controllers\SaleController::class, 'checkout'],
        '/settings/save' => [\App\Controllers\SettingsController::class, 'save'],
        '/settings/change-password' => [\App\Controllers\AuthController::class, 'changePassword'],
    ]
];

// Check route exists
if (isset($routes[$requestMethod][$requestUri])) {
    $route = $routes[$requestMethod][$requestUri];
    $controllerName = $route[0];
    $actionName = $route[1];

    $controller = new $controllerName();
    $controller->$actionName();
} else {
    http_response_code(404);
    view('errors/404');
}
