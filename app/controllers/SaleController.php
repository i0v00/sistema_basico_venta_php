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
     * Show POS main screen
     */
    public function pos() {
        $categories = Product::getCategories();
        
        // Fetch only active products
        $allProducts = Product::all();
        $products = array_filter($allProducts, function($p) {
            return (int)$p['active'] === 1;
        });

        // Set selected category ID for UI default
        $selectedCategoryId = $_GET['category_id'] ?? ($categories[0]['id'] ?? null);

        view('sales/pos', [
            'categories' => $categories,
            'products' => $products,
            'selectedCategoryId' => $selectedCategoryId
        ]);
    }

    /**
     * Handle checkout (called via AJAX)
     */
    public function checkout() {
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
            // Process the sale
            $saleId = Sale::createSale($data['items']);
            
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
     * Show sales history
     */
    public function history() {
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        
        $sales = Sale::history($startDate, $endDate);

        view('sales/history', [
            'sales' => $sales,
            'startDate' => $startDate,
            'endDate' => $endDate
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
}
