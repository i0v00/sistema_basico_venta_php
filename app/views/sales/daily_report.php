<?php
/**
 * Daily Sales Report - Standalone printable page (no layout wrapper)
 * Route: /sales/daily-report?date=YYYY-MM-DD
 */
$totalRevenue      = $stats['day_revenue'] ?? 0;
$totalTransactions = $stats['day_count'] ?? 0;
$avgTicket         = $totalTransactions > 0 ? ($totalRevenue / $totalTransactions) : 0;

$dayName = date('l', strtotime($date));
$dayNamesES = [
    'Monday'=>'Lunes','Tuesday'=>'Martes','Wednesday'=>'Miércoles',
    'Thursday'=>'Jueves','Friday'=>'Viernes','Saturday'=>'Sábado','Sunday'=>'Domingo'
];
$formattedDayName = $dayNamesES[$dayName] ?? $dayName;
$formattedDate    = date('d \d\e F \d\e Y', strtotime($date));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Informe Diario — <?= htmlspecialchars($date) ?> — Duke's Fast Food</title>
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
        .stat-card .note{font-size:10.5px;color:#aaa;margin-top:2px}
        .content{padding:24px 32px}
        .section-title{font-size:11.5px;font-weight:800;text-transform:uppercase;letter-spacing:.7px;color:#7B4F2E;border-bottom:2px solid #E07B39;padding-bottom:5px;margin-bottom:13px}
        .two-col{display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:28px}
        table{width:100%;border-collapse:collapse}
        thead tr{background:#3D1C02;color:#fff}
        thead th{padding:9px 11px;text-align:left;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.4px}
        thead th.r{text-align:right}
        tbody tr{border-bottom:1px solid #f2e8de}
        tbody tr:nth-child(even){background:#fdf8f4}
        tbody td{padding:8px 11px;font-size:12px;color:#333}
        tbody td.r{text-align:right;font-weight:600}
        .rank{display:inline-flex;align-items:center;justify-content:center;width:19px;height:19px;border-radius:50%;font-size:10px;font-weight:900;background:#E07B39;color:#fff;margin-right:6px}
        .tx-block{margin-bottom:14px;border:1px solid #e8ddd3;border-radius:10px;overflow:hidden}
        .tx-head{background:#f9f4ef;padding:10px 14px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #e8ddd3}
        .tx-id{font-size:12px;font-weight:800;color:#3D1C02}
        .tx-time{font-size:11px;color:#999;margin-left:10px}
        .tx-cashier{font-size:11px;color:#777;margin-left:10px}
        .tx-total{font-size:15px;font-weight:900;color:#E07B39}
        .tx-items{padding:10px 14px}
        .item-row{display:flex;justify-content:space-between;align-items:center;padding:5px 0;border-bottom:1px dotted #ede0d4}
        .item-row:last-child{border-bottom:none}
        .item-name{font-size:12px;color:#333}
        .item-qty{font-size:11px;color:#999;margin:0 10px;white-space:nowrap}
        .item-sub{font-size:12px;font-weight:700;color:#555}
        .badge{display:inline-block;padding:2px 8px;border-radius:99px;font-size:10px;font-weight:700;text-transform:uppercase}
        .b-finalizado{background:#d1fae5;color:#065f46}
        .b-entregado{background:#dbeafe;color:#1e40af}
        .b-pendiente{background:#fef3c7;color:#92400e}
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
        <h1>📋 Informe de Ventas Diario</h1>
        <div class="sub"><?= $formattedDayName ?>, <?= $formattedDate ?> &nbsp;·&nbsp; Impreso: <?= date('d/m/Y H:i') ?></div>
    </div>

    <!-- Summary bar -->
    <div class="summary-bar">
        <div class="stat-card">
            <div class="label">Total del Día</div>
            <div class="value"><?= formatMoney($totalRevenue) ?></div>
            <div class="note">ingresos facturados</div>
        </div>
        <div class="stat-card">
            <div class="label">Transacciones</div>
            <div class="value"><?= (int)$totalTransactions ?></div>
            <div class="note">pedidos registrados</div>
        </div>
        <div class="stat-card">
            <div class="label">Ticket Promedio</div>
            <div class="value"><?= formatMoney($avgTicket) ?></div>
            <div class="note">por pedido</div>
        </div>
    </div>

    <div class="content">
        <!-- Two-col: top products + quick stats -->
        <div class="two-col">
            <!-- Top products -->
            <div>
                <div class="section-title">🏆 Productos Más Vendidos</div>
                <?php if (empty($topProducts)): ?>
                    <div class="empty">Sin datos de ventas</div>
                <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Producto</th>
                            <th class="r">Unidades</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topProducts as $i => $p): ?>
                        <tr>
                            <td><span class="rank"><?= $i+1 ?></span></td>
                            <td><?= htmlspecialchars($p['name']) ?></td>
                            <td class="r"><?= (int)$p['qty'] ?> unid.</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>

            <!-- Quick stats table -->
            <div>
                <div class="section-title">📊 Resumen General</div>
                <?php
                $totalItems = 0;
                foreach ($sales as $s) {
                    foreach ($s['items'] as $it) $totalItems += (int)$it['quantity'];
                }
                ?>
                <table>
                    <tbody>
                        <tr>
                            <td style="padding:9px 11px;color:#555;font-weight:600">Ingresos totales</td>
                            <td class="r" style="padding:9px 11px;font-size:16px;font-weight:900;color:#E07B39"><?= formatMoney($totalRevenue) ?></td>
                        </tr>
                        <tr>
                            <td style="padding:9px 11px;color:#555;font-weight:600">Pedidos procesados</td>
                            <td class="r" style="padding:9px 11px;font-weight:800;color:#3D1C02"><?= (int)$totalTransactions ?></td>
                        </tr>
                        <tr>
                            <td style="padding:9px 11px;color:#555;font-weight:600">Ticket promedio</td>
                            <td class="r" style="padding:9px 11px;font-weight:800;color:#3D1C02"><?= formatMoney($avgTicket) ?></td>
                        </tr>
                        <tr>
                            <td style="padding:9px 11px;color:#555;font-weight:600">Unidades vendidas</td>
                            <td class="r" style="padding:9px 11px;font-weight:800;color:#3D1C02"><?= $totalItems ?> unid.</td>
                        </tr>
                        <tr>
                            <td style="padding:9px 11px;color:#555;font-weight:600">Productos distintos</td>
                            <td class="r" style="padding:9px 11px;font-weight:800;color:#3D1C02"><?= count($topProducts) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Transaction detail -->
        <div class="section-title">🧾 Detalle de Transacciones — <?= count($sales) ?> pedidos</div>

        <?php if (empty($sales)): ?>
            <div class="empty">
                <div style="font-size:32px;margin-bottom:8px">🍟</div>
                No se registraron ventas en esta fecha.
            </div>
        <?php else: ?>
            <?php foreach ($sales as $sale): ?>
            <div class="tx-block">
                <div class="tx-head">
                    <div>
                        <span class="tx-id">Pedido #<?= $sale['id'] ?></span>
                        <span class="tx-time"><?= date('H:i', strtotime($sale['sale_date'])) ?></span>
                        <?php if (!empty($sale['cashier_name'])): ?>
                        <span class="tx-cashier">👤 <?= htmlspecialchars($sale['cashier_name']) ?></span>
                        <?php endif; ?>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px">
                        <span class="badge b-<?= $sale['status'] ?>"><?= $sale['status'] ?></span>
                        <span class="tx-total"><?= formatMoney($sale['total']) ?></span>
                    </div>
                </div>
                <div class="tx-items">
                    <?php foreach ($sale['items'] as $item): ?>
                    <div class="item-row">
                        <span class="item-name"><?= htmlspecialchars($item['product_name']) ?></span>
                        <span class="item-qty"><?= (int)$item['quantity'] ?> × <?= formatMoney($item['price']) ?></span>
                        <span class="item-sub"><?= formatMoney($item['price'] * $item['quantity']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="rpt-footer">
        <span>Duke's Fast Food © <?= date('Y') ?> — Informe confidencial de uso interno</span>
        <span>Generado el <?= date('d/m/Y') ?> a las <?= date('H:i') ?></span>
    </div>
</div>

<!-- Floating buttons (no print) -->
<div class="actions">
    <a href="<?= BASE_URL ?>/?date=<?= htmlspecialchars($date) ?>" class="btn-b">← Dashboard</a>
    <button class="btn-p" onclick="window.print()">🖨️ Imprimir / Guardar PDF</button>
</div>
</body>
</html>
