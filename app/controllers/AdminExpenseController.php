<?php
namespace App\Controllers;

use Core\Auth;
use Core\Database;
use App\Models\GastoFijo;
use App\Models\Compra;
use App\Models\RawMaterial;
use App\Models\Sale;
use Exception;

class AdminExpenseController {
    public function __construct() {
        Auth::requireRole('admin');
    }

    public function index() {
        $startDate = $_GET['start_date'] ?? date('Y-m-d');
        $endDate   = $_GET['end_date'] ?? date('Y-m-d');

        $gastosFijos = GastoFijo::all($startDate, $endDate);
        $compras = Compra::all($startDate, $endDate);
        $rawMaterials = RawMaterial::all();

        view('admin_expenses/index', [
            'gastosFijos' => $gastosFijos,
            'compras' => $compras,
            'rawMaterials' => $rawMaterials,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }

    // --- GASTOS FIJOS ---
    public function saveGastoFijo() {
        $nombre = trim($_POST['nombre'] ?? '');
        $precio = (float)($_POST['precio'] ?? 0);
        $fecha = $_POST['fecha'] ?? date('Y-m-d');

        if (empty($nombre) || $precio <= 0) {
            Auth::setFlash('error', 'Nombre y precio válido son requeridos para el gasto fijo.');
            redirect('/admin-expenses');
        }

        try {
            GastoFijo::create([
                'nombre' => $nombre,
                'precio' => $precio,
                'fecha'  => $fecha
            ]);
            Auth::setFlash('success', 'Gasto fijo registrado correctamente.');
        } catch (Exception $e) {
            Auth::setFlash('error', 'Error al registrar gasto fijo: ' . $e->getMessage());
        }

        redirect('/admin-expenses');
    }

    public function deleteGastoFijo() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            try {
                GastoFijo::delete($id);
                Auth::setFlash('success', 'Gasto fijo eliminado.');
            } catch (Exception $e) {
                Auth::setFlash('error', 'Error al eliminar gasto fijo: ' . $e->getMessage());
            }
        }
        redirect('/admin-expenses');
    }

    // --- COMPRAS DE MATERIA PRIMA ---
    public function saveCompra() {
        $rawMaterialId = (int)($_POST['raw_material_id'] ?? 0);
        $cantidad = (float)($_POST['cantidad'] ?? 0);
        $precioUnitario = (float)($_POST['precio_unitario'] ?? 0);
        $fecha = $_POST['fecha'] ?? date('Y-m-d');

        if ($rawMaterialId <= 0 || $cantidad <= 0 || $precioUnitario <= 0) {
            Auth::setFlash('error', 'Seleccione un insumo, cantidad y precio válidos para la compra.');
            redirect('/admin-expenses');
        }

        $total = $cantidad * $precioUnitario;

        try {
            Compra::create([
                'raw_material_id' => $rawMaterialId,
                'cantidad' => $cantidad,
                'precio_unitario' => $precioUnitario,
                'total' => $total,
                'fecha' => $fecha
            ]);
            Auth::setFlash('success', 'Compra de materia prima registrada. Stock e informe actualizados.');
        } catch (Exception $e) {
            Auth::setFlash('error', 'Error al registrar la compra: ' . $e->getMessage());
        }

        redirect('/admin-expenses');
    }

    public function deleteCompra() {
        $id = $_POST['id'] ?? null;
        if ($id) {
            try {
                Compra::delete($id);
                Auth::setFlash('success', 'Registro de compra eliminado y stock descontado.');
            } catch (Exception $e) {
                Auth::setFlash('error', 'Error al eliminar la compra: ' . $e->getMessage());
            }
        }
        redirect('/admin-expenses');
    }

    // --- REPORTES FINANCIEROS Y BALANCES ---
    public function reports() {
        $filterMode = $_GET['filter_mode'] ?? 'day';
        $selectedMonth = sprintf('%02d', (int)($_GET['month'] ?? date('m')));
        $selectedYear  = (int)($_GET['year'] ?? date('Y'));
        
        if ($filterMode === 'week') {
            $startDate = date('Y-m-d', strtotime('-6 days'));
            $endDate   = date('Y-m-d');
        } elseif ($filterMode === 'month') {
            $startDate = "$selectedYear-$selectedMonth-01";
            $endDate   = date('Y-m-t', strtotime($startDate));
        } elseif ($filterMode === 'range') {
            $startDate = $_GET['start_date'] ?? date('Y-m-d');
            $endDate   = $_GET['end_date'] ?? date('Y-m-d');
        } else { // default 'day'
            $startDate = $_GET['date'] ?? date('Y-m-d');
            $endDate   = $startDate;
        }

        // Revenue (Ingresos por ventas)
        $totalRevenue = Sale::getTotalRevenueForRange($startDate, $endDate);
        $revenueByPayment = Sale::getRevenueByPaymentMethodForRange($startDate, $endDate);

        // Expense Totals (Gastos Fijos + Compras)
        $totalGastosFijos = GastoFijo::getTotalForRange($startDate, $endDate);
        $totalCompras     = Compra::getTotalForRange($startDate, $endDate);

        $totalExpenses = $totalGastosFijos + $totalCompras;
        $totalNet = $totalRevenue - $totalExpenses;

        // Fetch details
        $gastosFijos = GastoFijo::all($startDate, $endDate);
        $compras     = Compra::all($startDate, $endDate);

        $db = \Core\Database::getConnection();
        $stmt = $db->prepare("
            SELECT s.id, s.total, s.items_count, s.status, s.payment_method, s.sale_date, u.full_name as cashier_name 
            FROM sales s 
            LEFT JOIN users u ON s.cashier_id = u.id 
            WHERE DATE(s.sale_date) >= ? AND DATE(s.sale_date) <= ? AND s.deleted = 0 
            ORDER BY s.sale_date DESC
        ");
        $stmt->execute([$startDate, $endDate]);
        $sales = $stmt->fetchAll();

        $countEfectivo = count(array_filter($sales, fn($s) => strtolower($s['payment_method'] ?? '') === 'efectivo'));
        $countQr       = count(array_filter($sales, fn($s) => strtolower($s['payment_method'] ?? '') === 'qr'));

        // Revenue by category
        $revenueByCategory = Sale::getRevenueByCategory($startDate, $endDate);

        view('reports/reports', [
            'filterMode' => $filterMode,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalRevenue' => $totalRevenue,
            'revenueByPayment' => $revenueByPayment,
            'countEfectivo' => $countEfectivo,
            'countQr' => $countQr,
            'totalGastosFijos' => $totalGastosFijos,
            'totalCompras' => $totalCompras,
            'totalExpenses' => $totalExpenses,
            'totalNet' => $totalNet,
            'gastosFijos' => $gastosFijos,
            'compras' => $compras,
            'sales' => $sales,
            'revenueByCategory' => $revenueByCategory
        ]);
    }

    public function printReport() {
        $filterMode = $_GET['filter_mode'] ?? 'day';
        $selectedMonth = sprintf('%02d', (int)($_GET['month'] ?? date('m')));
        $selectedYear  = (int)($_GET['year'] ?? date('Y'));
        
        if ($filterMode === 'week') {
            $startDate = date('Y-m-d', strtotime('-6 days'));
            $endDate   = date('Y-m-d');
        } elseif ($filterMode === 'month') {
            $startDate = "$selectedYear-$selectedMonth-01";
            $endDate   = date('Y-m-t', strtotime($startDate));
        } elseif ($filterMode === 'range') {
            $startDate = $_GET['start_date'] ?? date('Y-m-d');
            $endDate   = $_GET['end_date'] ?? date('Y-m-d');
        } else { // default 'day'
            $startDate = $_GET['date'] ?? date('Y-m-d');
            $endDate   = $startDate;
        }

        $totalRevenue = Sale::getTotalRevenueForRange($startDate, $endDate);
        $revenueByPayment = Sale::getRevenueByPaymentMethodForRange($startDate, $endDate);

        $totalGastosFijos = GastoFijo::getTotalForRange($startDate, $endDate);
        $totalCompras     = Compra::getTotalForRange($startDate, $endDate);

        $totalExpenses = $totalGastosFijos + $totalCompras;
        $totalNet = $totalRevenue - $totalExpenses;

        $gastosFijos = GastoFijo::all($startDate, $endDate);
        $compras     = Compra::all($startDate, $endDate);
        
        $db = \Core\Database::getConnection();
        $stmt = $db->prepare("
            SELECT s.id, s.total, s.items_count, s.status, s.payment_method, s.sale_date, u.full_name as cashier_name 
            FROM sales s 
            LEFT JOIN users u ON s.cashier_id = u.id 
            WHERE DATE(s.sale_date) >= ? AND DATE(s.sale_date) <= ? AND s.deleted = 0 
            ORDER BY s.sale_date DESC
        ");
        $stmt->execute([$startDate, $endDate]);
        $sales = $stmt->fetchAll();

        $countEfectivo = count(array_filter($sales, fn($s) => strtolower($s['payment_method'] ?? '') === 'efectivo'));
        $countQr       = count(array_filter($sales, fn($s) => strtolower($s['payment_method'] ?? '') === 'qr'));

        // Revenue by category for print report
        $revenueByCategory = Sale::getRevenueByCategory($startDate, $endDate);

        viewRaw('reports/print', [
            'filterMode' => $filterMode,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalRevenue' => $totalRevenue,
            'revenueByPayment' => $revenueByPayment,
            'countEfectivo' => $countEfectivo,
            'countQr' => $countQr,
            'totalGastosFijos' => $totalGastosFijos,
            'totalCompras' => $totalCompras,
            'totalExpenses' => $totalExpenses,
            'totalNet' => $totalNet,
            'gastosFijos' => $gastosFijos,
            'compras' => $compras,
            'sales' => $sales,
            'revenueByCategory' => $revenueByCategory
        ]);
    }

    /**
     * Generate and download a full SQL backup of the database via PDO.
     * Does not require mysqldump binary (compatible with shared hosting).
     */
    public function backup() {
        $db = Database::getConnection();
        $dbName = $_ENV['DB_NAME'] ?? 'dukes_cakes_venta';
        $filename = 'backup_' . $dbName . '_' . date('Y-m-d_His') . '.sql';

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // Header comment
        $header = "-- ============================================\n";
        $header .= "-- Backup de Base de Datos: {$dbName}\n";
        $header .= "-- Generado: " . date('d/m/Y H:i:s') . "\n";
        $header .= "-- Sistema: Dukes Cakes POS\n";
        $header .= "-- ============================================\n\n";
        $header .= "SET FOREIGN_KEY_CHECKS=0;\n";
        $header .= "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n";
        $header .= "SET NAMES utf8mb4;\n\n";
        fwrite($output, $header);

        // Get all tables
        $tablesStmt = $db->query("SHOW TABLES");
        $tables = $tablesStmt->fetchAll(\PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            // DROP + CREATE TABLE
            fwrite($output, "-- -------------------------------------------\n");
            fwrite($output, "-- Tabla: `{$table}`\n");
            fwrite($output, "-- -------------------------------------------\n");
            fwrite($output, "DROP TABLE IF EXISTS `{$table}`;\n");

            $createStmt = $db->query("SHOW CREATE TABLE `{$table}`");
            $createRow  = $createStmt->fetch(\PDO::FETCH_ASSOC);
            $createSql  = $createRow['Create Table'] ?? '';
            fwrite($output, $createSql . ";\n\n");

            // Row data
            $rowsStmt = $db->query("SELECT * FROM `{$table}`");
            $rows = $rowsStmt->fetchAll(\PDO::FETCH_ASSOC);

            if (!empty($rows)) {
                // Build column list
                $columns = array_keys($rows[0]);
                $colList = '`' . implode('`, `', $columns) . '`';

                fwrite($output, "INSERT INTO `{$table}` ({$colList}) VALUES\n");

                $rowCount = count($rows);
                foreach ($rows as $i => $row) {
                    $values = array_map(function($val) use ($db) {
                        if ($val === null) return 'NULL';
                        return $db->quote((string)$val);
                    }, array_values($row));

                    $comma = ($i < $rowCount - 1) ? ',' : ';';
                    fwrite($output, '(' . implode(', ', $values) . ")" . $comma . "\n");
                }
                fwrite($output, "\n");
            }
        }

        fwrite($output, "\nSET FOREIGN_KEY_CHECKS=1;\n");
        fwrite($output, "-- Fin del backup.\n");
        fclose($output);
        exit;
    }
}
