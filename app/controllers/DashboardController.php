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
        $selectedDate = $_GET['date'] ?? date('Y-m-d');
        
        $stats = Sale::getStatsForDate($selectedDate);
        
        // Fetch low stock items
        $allMaterials = RawMaterial::all();
        $lowStockMaterials = array_filter($allMaterials, function($m) {
            return $m['current_stock'] <= $m['min_stock'];
        });

        // Charts data
        $chartSales = Sale::getSalesChartDataForDate($selectedDate);
        $topProducts = Sale::getTopProductsForDate($selectedDate);

        view('dashboard', [
            'stats' => $stats,
            'lowStockMaterials' => $lowStockMaterials,
            'chartSales' => $chartSales,
            'topProducts' => $topProducts,
            'selectedDate' => $selectedDate
        ]);
    }
}
