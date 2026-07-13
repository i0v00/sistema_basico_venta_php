<?php
namespace App\Controllers;

use Core\Auth;
use App\Models\Sale;
use App\Models\Product;
use Exception;

class SaleController {
    public function __construct() {
        Auth::requireLogin();
    }

    /**
     * Show POS main screen (caja + admin only)
     */
    public function pos() {
        Auth::requireRole(['caja']);
        $categories = Product::getCategories();
        
        // Fetch only active products
        $allProducts = Product::all();
        $products = array_filter($allProducts, function($p) {
            return (int)$p['active'] === 1;
        });

        // Set selected category ID for UI default
        $selectedCategoryId = $_GET['category_id'] ?? ($categories[0]['id'] ?? null);

        view('sales/pos', [
            'categories'         => $categories,
            'products'           => $products,
            'selectedCategoryId' => $selectedCategoryId
        ]);
    }

    /**
     * Handle checkout (called via AJAX)
     */
    public function checkout() {
        Auth::requireRole(['caja']);
        header('Content-Type: application/json');

        // Check raw input
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (!$data || empty($data['items'])) {
            echo json_encode([
                'success' => false,
                'message' => 'El carrito está vacío o la solicitud es inválida.'
            ]);
            exit;
        }

        try {
            $user   = Auth::user();
            $cashierId = $user ? (int)$user['id'] : 0;

            // Process the sale
            $saleId = Sale::createSale($data['items'], $cashierId);
            
            echo json_encode([
                'success' => true,
                'message' => 'Venta registrada con éxito.',
                'sale_id' => $saleId
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * Show the live orders screen (caja + cocinero + admin)
     */
    public function orders() {
        Auth::requireRole(['caja', 'cocinero']);
        view('sales/orders', []);
    }

    /**
     * JSON endpoint for polling active orders (today)
     */
    public function ordersJson() {
        Auth::requireRole(['caja', 'cocinero']);
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, no-store, must-revalidate');

        $status = $_GET['status'] ?? null;
        if ($status === 'all') $status = null;

        $orders = Sale::getActiveOrders($status);

        echo json_encode([
            'success'   => true,
            'orders'    => $orders,
            'timestamp' => time(),
        ]);
        exit;
    }

    /**
     * Update an order's status via AJAX POST
     */
    public function updateOrderStatus() {
        Auth::requireRole(['caja', 'cocinero']);
        header('Content-Type: application/json');

        $input  = file_get_contents('php://input');
        $data   = json_decode($input, true);
        $saleId = (int)($data['sale_id'] ?? 0);
        $status = $data['status'] ?? '';

        if (!$saleId || !in_array($status, ['pendiente', 'entregado', 'finalizado'], true)) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos.']);
            exit;
        }

        $ok = Sale::updateStatus($saleId, $status);
        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Estado actualizado.' : 'No se pudo actualizar.',
        ]);
        exit;
    }

    /**
     * Show manual order form
     */
    public function createManualForm() {
        Auth::requireRole(['caja']);
        $products = Product::all();
        // filter active products
        $products = array_filter($products, function($p) {
            return (int)$p['active'] === 1;
        });
        view('sales/create_manual', [
            'products' => $products
        ]);
    }

    /**
     * Save manual order
     */
    public function saveManual() {
        Auth::requireRole(['caja']);
        $saleDate = $_POST['sale_date'] ?? null;
        $itemsRaw = $_POST['items'] ?? [];
        
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

        if (empty($saleDate) || empty($itemsRaw)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Por favor complete todos los datos del pedido.']);
                exit;
            }
            setFlash('error', 'Por favor complete todos los datos del pedido.');
            redirect('/sales/create-manual');
        }

        // Format items from form to cartItems format
        $cartItems = [];
        foreach ($itemsRaw as $productId => $qty) {
            $qty = (int)$qty;
            if ($qty <= 0) continue;

            $product = Product::find($productId);
            if ($product) {
                $cartItems[] = [
                    'id' => (int)$product['id'],
                    'price' => (float)$product['price'],
                    'quantity' => $qty
                ];
            }
        }

        if (empty($cartItems)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Debe agregar al menos un producto con cantidad mayor a 0.']);
                exit;
            }
            setFlash('error', 'Debe agregar al menos un producto con cantidad mayor a 0.');
            redirect('/sales/create-manual');
        }

        try {
            $user = Auth::user();
            $cashierId = $user ? (int)$user['id'] : 0;

            // Save sale with custom date and status finalizado so it doesn't clutter live active orders screen
            Sale::createSale($cartItems, $cashierId, $saleDate, 'finalizado');
            
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Pedido histórico registrado correctamente.']);
                exit;
            }
            
            setFlash('success', 'Pedido histórico registrado correctamente.');
            redirect('/sales/history');
        } catch (Exception $e) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Error al guardar: ' . $e->getMessage()]);
                exit;
            }
            setFlash('error', 'Error al guardar: ' . $e->getMessage());
            redirect('/sales/create-manual');
        }
    }

    /**
     * Soft delete an order
     */
    public function deleteSale() {
        Auth::requireRole(['admin']);
        $saleId = (int)($_POST['sale_id'] ?? 0);
        if ($saleId <= 0) {
            setFlash('error', 'ID de pedido inválido.');
            redirect('/sales/history');
        }

        if (Sale::delete($saleId)) {
            setFlash('success', 'Pedido eliminado lógicamente de los registros.');
        } else {
            setFlash('error', 'No se pudo eliminar el pedido.');
        }
        redirect('/sales/history');
    }

    /**
     * Show sales history
     */
    public function history() {
        Auth::requireRole(['caja']);
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate   = $_GET['end_date'] ?? date('Y-m-d');
        
        $sales = Sale::history($startDate, $endDate);

        view('sales/history', [
            'sales'     => $sales,
            'startDate' => $startDate,
            'endDate'   => $endDate
        ]);
    }

    /**
     * Get sale details as JSON (for visual modals)
     */
    public function details() {
        header('Content-Type: application/json');
        $saleId = $_GET['id'] ?? null;

        if (!$saleId) {
            echo json_encode(['success' => false, 'message' => 'ID de venta requerido.']);
            exit;
        }

        $details = Sale::getDetails($saleId);
        echo json_encode([
            'success' => true,
            'details' => $details
        ]);
        exit;
    }

    /**
     * Generate a printable daily sales report
     */
    public function dailyReport() {
        Auth::requireRole(['caja']);
        $date = $_GET['date'] ?? date('Y-m-d');

        $stats       = Sale::getStatsForDate($date);
        $topProducts = Sale::getTopProductsForDate($date);
        $sales       = Sale::getDailySalesDetail($date);

        viewRaw('sales/daily_report', [
            'date'        => $date,
            'stats'       => $stats,
            'topProducts' => $topProducts,
            'sales'       => $sales,
        ]);
    }
}
