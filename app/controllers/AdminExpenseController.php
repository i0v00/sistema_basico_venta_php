<?php
namespace App\Controllers;

use Core\Auth;
use App\Models\AdminProduct;
use App\Models\AdminExpense;
use App\Models\Sale;
use Exception;

class AdminExpenseController {
    public function __construct() {
        Auth::requireRole('admin');
    }

    public function index() {
        $startDate = $_GET['start_date'] ?? date('Y-m-d');
        $endDate   = $_GET['end_date'] ?? date('Y-m-d');

        $products = AdminProduct::all(); // Includes both active and inactive
        $activeProducts = AdminProduct::all(true); // Only active for registering new expenses
        $expenses = AdminExpense::all($startDate, $endDate);

        view('admin_expenses/index', [
            'products' => $products,
            'activeProducts' => $activeProducts,
            'expenses' => $expenses,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }

    public function saveProduct() {
        $id = $_POST['id'] ?? null;
        $nombre = trim($_POST['nombre'] ?? '');
        $precioUnitario = (float)($_POST['precio_unitario'] ?? 0);
        $activo = isset($_POST['activo']) ? 1 : 0;

        if (empty($nombre) || $precioUnitario < 0) {
            Auth::setFlash('error', 'Nombre y precio válido son requeridos.');
            redirect('/admin-expenses');
        }

        $data = [
            'nombre' => $nombre,
            'precio_unitario' => $precioUnitario,
            'activo' => $activo
        ];

        try {
            if ($id) {
                AdminProduct::update($id, $data);
                Auth::setFlash('success', 'Producto administrativo actualizado.');
            } else {
                AdminProduct::create($data);
                Auth::setFlash('success', 'Producto administrativo creado.');
            }
        } catch (Exception $e) {
            Auth::setFlash('error', 'Error al guardar producto: ' . $e->getMessage());
        }

        redirect('/admin-expenses');
    }

    public function deleteProduct() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            try {
                AdminProduct::delete($id);
                Auth::setFlash('success', 'Producto administrativo eliminado (lógicamente).');
            } catch (Exception $e) {
                Auth::setFlash('error', 'Error al eliminar producto: ' . $e->getMessage());
            }
        }
        redirect('/admin-expenses');
    }

    public function saveExpense() {
        $id = $_POST['id'] ?? null;
        $productoId = (int)($_POST['producto_id'] ?? 0);
        $cantidad = (int)($_POST['cantidad'] ?? 1);
        $precioUnitario = (float)($_POST['precio_unitario'] ?? 0);
        $fecha = $_POST['fecha'] ?? date('Y-m-d');

        if ($productoId <= 0 || $cantidad <= 0 || $precioUnitario <= 0) {
            Auth::setFlash('error', 'Producto, cantidad y precio unitario son requeridos.');
            redirect('/admin-expenses');
        }

        $total = $cantidad * $precioUnitario;
        $data = [
            'producto_id' => $productoId,
            'cantidad' => $cantidad,
            'precio_unitario' => $precioUnitario,
            'total' => $total,
            'fecha' => $fecha
        ];

        try {
            if ($id) {
                AdminExpense::update($id, $data);
                Auth::setFlash('success', 'Gasto administrativo actualizado.');
            } else {
                AdminExpense::create($data);
                Auth::setFlash('success', 'Gasto administrativo registrado.');
            }
        } catch (Exception $e) {
            Auth::setFlash('error', 'Error al registrar gasto: ' . $e->getMessage());
        }

        redirect('/admin-expenses');
    }

    public function deleteExpense() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            try {
                AdminExpense::delete($id);
                Auth::setFlash('success', 'Gasto administrativo eliminado permanentemente.');
            } catch (Exception $e) {
                Auth::setFlash('error', 'Error al eliminar el gasto: ' . $e->getMessage());
            }
        }
        redirect('/admin-expenses');
    }

    public function reports() {
        // Filter modes: day, week, range
        $filterMode = $_GET['filter_mode'] ?? 'day';
        
        if ($filterMode === 'week') {
            $startDate = date('Y-m-d', strtotime('-6 days'));
            $endDate   = date('Y-m-d');
        } elseif ($filterMode === 'range') {
            $startDate = $_GET['start_date'] ?? date('Y-m-d');
            $endDate   = $_GET['end_date'] ?? date('Y-m-d');
        } else { // default 'day'
            $startDate = $_GET['date'] ?? date('Y-m-d');
            $endDate   = $startDate;
        }

        // Get total sales revenue
        $totalRevenue = Sale::getTotalRevenueForRange($startDate, $endDate);
        // Get total administrative expenses
        $totalExpenses = AdminExpense::getTotalExpensesForRange($startDate, $endDate);
        // Net total
        $totalNet = $totalRevenue - $totalExpenses;

        // Fetch records detail
        $expenses = AdminExpense::all($startDate, $endDate);
        
        // Fetch sales detail for the range (custom query or loop)
        $db = \Core\Database::getConnection();
        $stmt = $db->prepare("
            SELECT s.id, s.total, s.items_count, s.status, s.sale_date, u.full_name as cashier_name 
            FROM sales s 
            LEFT JOIN users u ON s.cashier_id = u.id 
            WHERE DATE(s.sale_date) >= ? AND DATE(s.sale_date) <= ? AND s.deleted = 0 
            ORDER BY s.sale_date DESC
        ");
        $stmt->execute([$startDate, $endDate]);
        $sales = $stmt->fetchAll();

        view('reports/reports', [
            'filterMode' => $filterMode,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalRevenue' => $totalRevenue,
            'totalExpenses' => $totalExpenses,
            'totalNet' => $totalNet,
            'expenses' => $expenses,
            'sales' => $sales
        ]);
    }

    public function printReport() {
        $filterMode = $_GET['filter_mode'] ?? 'day';
        
        if ($filterMode === 'week') {
            $startDate = date('Y-m-d', strtotime('-6 days'));
            $endDate   = date('Y-m-d');
        } elseif ($filterMode === 'range') {
            $startDate = $_GET['start_date'] ?? date('Y-m-d');
            $endDate   = $_GET['end_date'] ?? date('Y-m-d');
        } else { // default 'day'
            $startDate = $_GET['date'] ?? date('Y-m-d');
            $endDate   = $startDate;
        }

        $totalRevenue = Sale::getTotalRevenueForRange($startDate, $endDate);
        $totalExpenses = AdminExpense::getTotalExpensesForRange($startDate, $endDate);
        $totalNet = $totalRevenue - $totalExpenses;

        $expenses = AdminExpense::all($startDate, $endDate);
        
        $db = \Core\Database::getConnection();
        $stmt = $db->prepare("
            SELECT s.id, s.total, s.items_count, s.status, s.sale_date, u.full_name as cashier_name 
            FROM sales s 
            LEFT JOIN users u ON s.cashier_id = u.id 
            WHERE DATE(s.sale_date) >= ? AND DATE(s.sale_date) <= ? AND s.deleted = 0 
            ORDER BY s.sale_date DESC
        ");
        $stmt->execute([$startDate, $endDate]);
        $sales = $stmt->fetchAll();

        viewRaw('reports/print', [
            'filterMode' => $filterMode,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalRevenue' => $totalRevenue,
            'totalExpenses' => $totalExpenses,
            'totalNet' => $totalNet,
            'expenses' => $expenses,
            'sales' => $sales
        ]);
    }
}
