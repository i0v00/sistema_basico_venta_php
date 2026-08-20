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
            $paymentMethod = $data['payment_method'] ?? null;

            if (empty($paymentMethod) || !in_array($paymentMethod, ['efectivo', 'qr'], true)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Debes seleccionar obligatoriamente un tipo de pago (Efectivo o QR).'
                ]);
                exit;
            }

            // Process the sale
            $saleId = Sale::createSale($data['items'], $cashierId, null, 'pendiente', $paymentMethod);
            
            echo json_encode([
                'success' => true,
                'message' => 'Venta registrada con éxito.',
                'sale_id' => $saleId,
                'payment_method' => $paymentMethod
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
     * Update payment method for a sale (used when assigning method to unspecified sales)
     */
    public function updatePaymentMethod() {
        Auth::requireRole(['admin', 'caja']);
        $saleId = (int)($_POST['sale_id'] ?? 0);
        $paymentMethod = $_POST['payment_method'] ?? '';
        $redirectUrl = $_POST['redirect_url'] ?? '/reports';
        
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

        if ($saleId > 0 && in_array($paymentMethod, ['efectivo', 'qr'], true)) {
            Sale::updatePaymentMethod($saleId, $paymentMethod);
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => "Tipo de pago actualizado"]);
                exit;
            }
            Auth::setFlash('success', "Tipo de pago actualizado a " . ($paymentMethod === 'qr' ? 'QR' : 'Efectivo') . " para la venta #{$saleId}.");
        } else {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => "No se pudo actualizar el tipo de pago"]);
                exit;
            }
            Auth::setFlash('error', "No se pudo actualizar el tipo de pago.");
        }
        redirect($redirectUrl);
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
        $categories = Product::getCategories();
        $products = Product::all();
        // filter active products
        $products = array_filter($products, function($p) {
            return (int)$p['active'] === 1;
        });
        view('sales/create_manual', [
            'categories' => $categories,
            'products'   => $products
        ]);
    }

    /**
     * Save manual order
     */
    public function saveManual() {
        Auth::requireRole(['caja']);
        $saleDate = $_POST['sale_date'] ?? null;
        $paymentMethod = $_POST['payment_method'] ?? null;
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

        if (empty($paymentMethod) || !in_array($paymentMethod, ['efectivo', 'qr'], true)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Debes seleccionar obligatoriamente un tipo de pago (Efectivo o QR).']);
                exit;
            }
            setFlash('error', 'Debes seleccionar obligatoriamente un tipo de pago (Efectivo o QR).');
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
            Sale::createSale($cartItems, $cashierId, $saleDate, 'finalizado', $paymentMethod);
            
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
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

        if ($saleId <= 0) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'ID de pedido inválido.']);
                exit;
            }
            setFlash('error', 'ID de pedido inválido.');
            redirect('/sales/history');
        }

        if (Sale::delete($saleId)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Pedido eliminado lógicamente de los registros.']);
                exit;
            }
            setFlash('success', 'Pedido eliminado lógicamente de los registros.');
        } else {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'No se pudo eliminar el pedido.']);
                exit;
            }
            setFlash('error', 'No se pudo eliminar el pedido.');
        }
        
        if (!$isAjax) {
            redirect('/sales/history');
        }
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
     * Export sales history as CSV download
     */
    public function exportCsv() {
        Auth::requireRole(['caja']);
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate   = $_GET['end_date'] ?? date('Y-m-d');

        $rows = Sale::exportHistoryData($startDate, $endDate);

        $filename = 'ventas_' . $startDate . '_al_' . $endDate . '.csv';
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');

        $out = fopen('php://output', 'w');
        // UTF-8 BOM so Excel opens correctly
        fputs($out, "\xEF\xBB\xBF");

        fputcsv($out, ['N° Ticket', 'Fecha y Hora', 'Método Pago', 'Cajero', 'Cantidad Items', 'Total (Bs.)', 'Estado', 'Detalle Productos'], ';');

        foreach ($rows as $row) {
            fputcsv($out, [
                '#' . $row['id'],
                date('d/m/Y H:i', strtotime($row['sale_date'])),
                strtoupper($row['payment_method'] ?? 'Sin Especificar'),
                $row['cashier_name'] ?? '-',
                $row['items_count'],
                number_format((float)$row['total'], 2, '.', ''),
                ucfirst($row['status'] ?? ''),
                $row['items_detail'] ?? ''
            ], ';');
        }
        fclose($out);
        exit;
    }

    /**
     * Export sales history as Excel (.xls) download
     */
    public function exportExcel() {
        Auth::requireRole(['caja']);
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate   = $_GET['end_date'] ?? date('Y-m-d');

        $rows = Sale::exportHistoryData($startDate, $endDate);

        $filename = 'ventas_' . $startDate . '_al_' . $endDate . '.xls';
        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        // HTML table trick – Excel opens this natively
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" ';
        echo 'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . "\n";
        echo '<Worksheet ss:Name="Ventas"><Table>' . "\n";

        // Header row
        $headers = ['N° Ticket', 'Fecha y Hora', 'Método Pago', 'Cajero', 'Cantidad Items', 'Total (Bs.)', 'Estado', 'Detalle Productos'];
        echo '<Row>';
        foreach ($headers as $h) {
            echo '<Cell ss:StyleID="s62"><Data ss:Type="String">' . htmlspecialchars($h) . '</Data></Cell>';
        }
        echo '</Row>' . "\n";

        foreach ($rows as $row) {
            $cols = [
                '#' . $row['id'],
                date('d/m/Y H:i', strtotime($row['sale_date'])),
                strtoupper($row['payment_method'] ?? 'Sin Especificar'),
                $row['cashier_name'] ?? '-',
                (int)$row['items_count'],
                number_format((float)$row['total'], 2, '.', ''),
                ucfirst($row['status'] ?? ''),
                $row['items_detail'] ?? ''
            ];
            echo '<Row>';
            foreach ($cols as $c) {
                echo '<Cell><Data ss:Type="String">' . htmlspecialchars((string)$c) . '</Data></Cell>';
            }
            echo '</Row>' . "\n";
        }

        echo '</Table></Worksheet></Workbook>';
        exit;
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

        $sale = Sale::findById((int)$saleId);
        $details = Sale::getDetails($saleId);
        
        $pm = strtolower($sale['payment_method'] ?? '');
        $validPm = in_array($pm, ['efectivo', 'qr'], true) ? $pm : null;

        echo json_encode([
            'success' => true,
            'payment_method' => $validPm,
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
