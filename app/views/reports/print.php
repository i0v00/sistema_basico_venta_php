<?php
/**
 * General Financial Balance Report - Standalone printable page (no layout wrapper)
 * Route: /reports/print
 */
$formattedPeriod = '';
if ($filterMode === 'week') {
    $formattedPeriod = 'Semanal (' . date('d/m/Y', strtotime($startDate)) . ' al ' . date('d/m/Y', strtotime($endDate)) . ')';
} elseif ($filterMode === 'range') {
    $formattedPeriod = 'Período (' . date('d/m/Y', strtotime($startDate)) . ' al ' . date('d/m/Y', strtotime($endDate)) . ')';
} else {
    $formattedPeriod = 'Diario (' . date('d/m/Y', strtotime($startDate)) . ')';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Informe de Balance General — Duke's Fast Food</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Segoe UI',Arial,sans-serif;font-size:13px;color:#1a1a1a;background:#f5ede4}
        .page{max-width:860px;margin:30px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 8px 32px rgba(0,0,0,.12)}
        .report-header{background:linear-gradient(135deg,#3D1C02,#7B4F2E);color:#fff;padding:28px 32px 22px}
        .brand{display:flex;align-items:center;gap:10px;margin-bottom:14px}
        .brand-name{font-size:19px;font-weight:900;letter-spacing:-0.3px}
        .brand-name span{color:#E07B39}
        .report-header h1{font-size:22px;font-weight:800;margin-bottom:4px}
        .report-header .sub{color:rgba(255,255,255,.65);font-size:12px}
        .summary-bar{display:grid;grid-template-columns:repeat(3,1fr);border-bottom:1px solid #ede0d4}
        .stat-card{padding:18px 24px;text-align:center;border-right:1px solid #ede0d4}
        .stat-card:last-child{border-right:none}
        .stat-card .label{font-size:9.5px;font-weight:800;text-transform:uppercase;letter-spacing:.8px;color:#7B4F2E;margin-bottom:4px}
        .stat-card .value{font-size:22px;font-weight:900;color:#3D1C02}
        .stat-card .value.revenue{color:#2d6a4f}
        .stat-card .value.expense{color:#9b2c2c}
        .stat-card .value.net-positive{color:#3D1C02}
        .stat-card .value.net-negative{color:#c53030}
        .stat-card .note{font-size:10.5px;color:#aaa;margin-top:2px}
        .content{padding:24px 32px}
        .section-title{font-size:11.5px;font-weight:800;text-transform:uppercase;letter-spacing:.7px;color:#7B4F2E;border-bottom:2px solid #E07B39;padding-bottom:5px;margin-bottom:13px}
        
        .grid-detail{display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:28px}
        table{width:100%;border-collapse:collapse}
        thead tr{background:#3D1C02;color:#fff}
        thead th{padding:9px 11px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px}
        thead th.r{text-align:right}
        tbody tr{border-bottom:1px solid #f2e8de}
        tbody tr:nth-child(even){background:#fdf8f4}
        tbody td{padding:8px 11px;font-size:12px;color:#333}
        tbody td.r{text-align:right;font-weight:600}

        .empty{text-align:center;padding:40px;color:#bbb}
        .rpt-footer{border-top:1px solid #ede0d4;padding:14px 32px;display:flex;justify-content:space-between;color:#bbb;font-size:11px}
        .actions{position:fixed;bottom:22px;right:22px;display:flex;gap:10px;z-index:99}
        .btn-p{background:#3D1C02;color:#fff;border:none;padding:12px 22px;border-radius:12px;font-weight:800;font-size:14px;cursor:pointer;box-shadow:0 4px 14px rgba(0,0,0,.2);transition:all .2s}
        .btn-p:hover{background:#5a2f0a;transform:translateY(-1px)}
        .btn-b{background:#fff;color:#3D1C02;border:2px solid #3D1C02;padding:12px 22px;border-radius:12px;font-weight:800;font-size:14px;cursor:pointer;text-decoration:none;transition:all .2s}
        .btn-b:hover{background:#f9f4ef}

        @media print{
            body{background:#fff}
            .page{max-width:100%;margin:0;border-radius:0;box-shadow:none}
            .actions{display:none!important}
        }
    </style>
</head>
<body>
<div class="page">
    <!-- Header -->
    <div class="report-header">
        <div class="brand">
            <span style="font-size:26px">🍔</span>
            <div>
                <div class="brand-name">DUKE'S <span>Fast Food</span></div>
                <div style="font-size:11px;opacity:.6">Sistema de Punto de Venta</div>
            </div>
        </div>
        <h1>📊 Informe de Balance General</h1>
        <div class="sub">Período: <?= htmlspecialchars($formattedPeriod) ?> &nbsp;·&nbsp; Impreso: <?= date('d/m/Y H:i') ?></div>
    </div>

    <!-- Summary bar -->
    <div class="summary-bar">
        <div class="stat-card">
            <div class="label">Ingresos Totales (Ventas)</div>
            <div class="value revenue"><?= formatMoney($totalRevenue) ?></div>
            <div class="note"><?= count($sales) ?> transacciones</div>
        </div>
        <div class="stat-card">
            <div class="label">Gastos Administrativos</div>
            <div class="value expense"><?= formatMoney($totalExpenses) ?></div>
            <div class="note"><?= count($expenses) ?> egresos</div>
        </div>
        <div class="stat-card">
            <div class="label">Balance Neto</div>
            <div class="value <?= $totalNet >= 0 ? 'net-positive' : 'net-negative' ?>">
                <?= formatMoney($totalNet) ?>
            </div>
            <div class="note">Ingresos - Egresos</div>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="content">
        <div class="grid-detail">
            <!-- Left Side: Sales -->
            <div>
                <h3 class="section-title">Ingresos por Ventas</h3>
                <table>
                    <thead>
                        <tr>
                            <th>N° Venta</th>
                            <th>Fecha</th>
                            <th class="r">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($sales)): ?>
                            <tr>
                                <td colspan="3" class="empty">No hay registros de ventas.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($sales as $sale): ?>
                                <tr>
                                    <td><strong>#<?= $sale['id'] ?></strong></td>
                                    <td><?= date('d/m/Y H:i', strtotime($sale['sale_date'])) ?></td>
                                    <td class="r"><?= formatMoney($sale['total']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Right Side: Expenses -->
            <div>
                <h3 class="section-title">Egresos por Gastos Admin</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Concepto</th>
                            <th class="r">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($expenses)): ?>
                            <tr>
                                <td colspan="3" class="empty">No hay registros de gastos.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($expenses as $exp): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($exp['fecha'])) ?></td>
                                    <td><strong><?= e($exp['producto_nombre']) ?></strong></td>
                                    <td class="r"><?= formatMoney($exp['total']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="rpt-footer">
        <div>Duke's Fast Food POS — Sistema de Balance Administrativo</div>
        <div>Pág. 1 de 1</div>
    </div>
</div>

<!-- Floating Action Buttons -->
<div class="actions">
    <button onclick="window.close()" class="btn-b">✕ Cerrar Vista</button>
    <button onclick="window.print()" class="btn-p">🖨️ Imprimir Reporte (PDF)</button>
</div>
</body>
</html>
