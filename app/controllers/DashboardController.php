<?php
namespace App\Controllers;

use Core\Auth;
use App\Models\Sale;
use App\Models\RawMaterial;

class DashboardController {
    public function __construct() {
        Auth::requireRole('admin');
    }

    public function index() {
        $stats = Sale::getStats();
        
        // Fetch low stock items
        $allMaterials = RawMaterial::all();
        $lowStockMaterials = array_filter($allMaterials, function($m) {
            return $m['current_stock'] <= $m['min_stock'];
        });

        // Charts data
        $chartSales = Sale::getSalesChartData();
        $topProducts = Sale::getTopProducts();

        view('dashboard', [
            'stats' => $stats,
            'lowStockMaterials' => $lowStockMaterials,
            'chartSales' => $chartSales,
            'topProducts' => $topProducts
        ]);
    }
}
