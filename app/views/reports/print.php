<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Financiero Ejecutivo - Duke's POS</title>
    <!-- Outfit & Inter Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        coffee: {
                            dark: '#3D1C02',
                            medium: '#7B4F2E',
                            light: '#A0714F',
                        },
                        cream: {
                            DEFAULT: '#FFF8F0',
                            dark: '#F5E6D3',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-shadow-none { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
            .page-break-inside-avoid { page-break-inside: avoid; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen p-4 md:p-8">

    <div class="max-w-5xl mx-auto space-y-6">

        <!-- Top Floating Actions Bar (Hidden on print) -->
        <div class="no-print bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="text-xl">🖨️</span>
                <span class="text-sm font-bold text-coffee-dark">Vista previa de impresión PDF / A4</span>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="window.close()" class="px-4 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                    Cerrar
                </button>
                <button onclick="window.print()" class="px-5 py-2 text-xs font-bold text-white bg-coffee-dark hover:bg-coffee-medium rounded-xl shadow-md transition flex items-center gap-2">
                    <span>🖨️</span> Imprimir / Guardar como PDF
                </button>
            </div>
        </div>

        <!-- Printable Document Wrapper -->
        <div class="bg-white p-6 sm:p-10 rounded-3xl border border-slate-200 shadow-md print-shadow-none space-y-8">

            <!-- Document Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center pb-6 border-b border-slate-200 gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-2xl">🍔</span>
                        <h1 class="text-2xl sm:text-3xl font-heading font-extrabold text-coffee-dark">DUKE'S FAST FOOD</h1>
                    </div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-coffee-medium">Reporte Financiero de Balance General</p>
                </div>
                <div class="text-left sm:text-right space-y-1">
                    <span class="inline-block px-3 py-1 bg-cream border border-cream-dark text-coffee-dark font-extrabold text-xs rounded-lg">
                        <?= $filterMode === 'month' ? 'Informe Mensual' : ($filterMode === 'week' ? 'Período Semanal' : ($filterMode === 'range' ? 'Rango Personalizado' : 'Período Diario')) ?>
                    </span>
                    <p class="text-xs text-slate-500 font-medium">
                        Del <span class="font-bold text-slate-700"><?= date('d/m/Y', strtotime($startDate)) ?></span> al <span class="font-bold text-slate-700"><?= date('d/m/Y', strtotime($endDate)) ?></span>
                    </p>
                    <p class="text-[10px] text-slate-400">Generado el <?= date('d/m/Y H:i') ?></p>
                </div>
            </div>

            <!-- Executive Balance Summary Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Total Revenue -->
                <div class="p-5 rounded-2xl bg-emerald-50/70 border border-emerald-200/80 flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold uppercase text-emerald-800 tracking-wider">Ingresos Totales (Ventas)</span>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-emerald-700 mt-1"><?= formatMoney($totalRevenue) ?></h2>
                        <span class="text-[11px] font-medium text-emerald-800/80"><?= count($sales) ?> ventas concretadas</span>
                    </div>
                    <div class="p-3 bg-emerald-100 text-emerald-700 rounded-xl text-xl font-bold">📈</div>
                </div>

                <!-- Total Expenses -->
                <div class="p-5 rounded-2xl bg-rose-50/70 border border-rose-200/80 flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold uppercase text-rose-800 tracking-wider">Egresos Totales</span>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-rose-700 mt-1"><?= formatMoney($totalExpenses) ?></h2>
                        <span class="text-[11px] font-medium text-rose-800/80">Compras + Gastos Fijos</span>
                    </div>
                    <div class="p-3 bg-rose-100 text-rose-700 rounded-xl text-xl font-bold">📉</div>
                </div>

                <!-- Net Total Balance -->
                <div class="p-5 rounded-2xl <?= $totalNet >= 0 ? 'bg-amber-50/70 border border-amber-200/80' : 'bg-rose-100/70 border border-rose-300' ?> flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold uppercase text-slate-700 tracking-wider">Balance / Ganancia Neta</span>
                        <h2 class="text-2xl sm:text-3xl font-extrabold <?= $totalNet >= 0 ? 'text-amber-800' : 'text-rose-700' ?> mt-1">
                            <?= formatMoney($totalNet) ?>
                        </h2>
                        <span class="text-[11px] font-medium text-slate-600">Total Ingresos - Total Egresos</span>
                    </div>
                    <div class="p-3 bg-white/80 rounded-xl text-xl">💰</div>
                </div>
            </div>

            <!-- Detailed Breakdown (Efectivo, QR, Compras, Gastos Fijos) -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
                <div class="p-3.5 bg-emerald-50/90 rounded-xl border border-emerald-200 flex flex-col justify-between">
                    <span class="font-bold text-emerald-900 text-[11px]">💵 Ganado en Efectivo:</span>
                    <span class="font-extrabold text-emerald-700 text-base mt-1"><?= formatMoney($revenueByPayment['efectivo'] ?? 0) ?></span>
                    <span class="text-[10px] text-emerald-800/70 font-medium"><?= $countEfectivo ?? 0 ?> ventas</span>
                </div>
                <div class="p-3.5 bg-blue-50/90 rounded-xl border border-blue-200 flex flex-col justify-between">
                    <span class="font-bold text-blue-900 text-[11px]">📱 Ganado por QR:</span>
                    <span class="font-extrabold text-blue-700 text-base mt-1"><?= formatMoney($revenueByPayment['qr'] ?? 0) ?></span>
                    <span class="text-[10px] text-blue-800/70 font-medium"><?= $countQr ?? 0 ?> ventas</span>
                </div>
                <div class="p-3.5 bg-slate-100 rounded-xl border border-slate-200 flex flex-col justify-between">
                    <span class="font-bold text-slate-700 text-[11px]">🛒 Compras Insumos:</span>
                    <span class="font-extrabold text-slate-800 text-base mt-1"><?= formatMoney($totalCompras) ?></span>
                    <span class="text-[10px] text-slate-500 font-medium"><?= count($compras) ?> registros</span>
                </div>
                <div class="p-3.5 bg-slate-100 rounded-xl border border-slate-200 flex flex-col justify-between">
                    <span class="font-bold text-slate-700 text-[11px]">📌 Gastos Fijos:</span>
                    <span class="font-extrabold text-slate-800 text-base mt-1"><?= formatMoney($totalGastosFijos) ?></span>
                    <span class="text-[10px] text-slate-500 font-medium"><?= count($gastosFijos) ?> registros</span>
                </div>
            </div>

            <!-- ══════════════════════════════════════════════════════
                 Section: VENTAS POR CATEGORÍA
            ══════════════════════════════════════════════════════ -->
            <?php if (!empty($revenueByCategory)): ?>
            <?php
            $totalCatRev = array_sum(array_column($revenueByCategory, 'total_revenue'));
            $maxCatRev   = max(array_column($revenueByCategory, 'total_revenue')) ?: 1;

            // Colour palette cycling for rows
            $rowColors = [
                ['bg' => '#FEF3C7', 'border' => '#FDE68A', 'text' => '#92400E'],  // amber
                ['bg' => '#DBEAFE', 'border' => '#BFDBFE', 'text' => '#1E40AF'],  // blue
                ['bg' => '#D1FAE5', 'border' => '#A7F3D0', 'text' => '#065F46'],  // emerald
                ['bg' => '#FCE7F3', 'border' => '#FBCFE8', 'text' => '#9D174D'],  // pink
                ['bg' => '#EDE9FE', 'border' => '#DDD6FE', 'text' => '#5B21B6'],  // purple
                ['bg' => '#FEE2E2', 'border' => '#FECACA', 'text' => '#991B1B'],  // red
                ['bg' => '#ECFCCB', 'border' => '#D9F99D', 'text' => '#3F6212'],  // lime
                ['bg' => '#E0F2FE', 'border' => '#BAE6FD', 'text' => '#075985'],  // sky
                ['bg' => '#F3F4F6', 'border' => '#E5E7EB', 'text' => '#374151'],  // gray
            ];

            // Map icon keys to compact emoji-like labels for print
            $iconLabels = [
                'hamburger'   => '🍔',
                'fries'       => '🍟',
                'chicken'     => '🍗',
                'drink_cup'   => '🥤',
                'soda_bottle' => '🥃',
                'package'     => '📦',
                'dessert'     => '🍮',
                'weekend'     => '⭐',
                'coffee'      => '☕',
                'pizza'       => '🍕',
                'salad'       => '🥗',
                'combo'       => '🗂️',
            ];
            ?>
            <div class="space-y-3 page-break-inside-avoid">
                <!-- Section title -->
                <h3 class="text-sm font-heading font-extrabold uppercase tracking-wider text-slate-800 flex items-center gap-2 border-b-2 border-amber-300 pb-2">
                    <span style="display:inline-block;width:1.1rem;height:1.1rem;vertical-align:middle;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                        </svg>
                    </span>
                    Ingresos por Categoría de Producto
                    <span class="ml-auto text-[11px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2.5 py-0.5 rounded-lg normal-case tracking-normal">
                        Total: <?= formatMoney($totalCatRev) ?>
                    </span>
                </h3>

                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-amber-50 text-amber-950 font-bold uppercase tracking-wider text-[10px]">
                                <th class="p-3 pl-4" style="width:2rem">#</th>
                                <th class="p-3">Categoría</th>
                                <th class="p-3 text-center" style="width:6rem">Items Vendidos</th>
                                <th class="p-3 text-right" style="width:8rem">Ganancia Total</th>
                                <th class="p-3 text-right" style="width:5rem">% del Total</th>
                                <th class="p-3" style="width:9rem">Participación</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($revenueByCategory as $idx => $catRow):
                                $color   = $rowColors[$idx % count($rowColors)];
                                $share   = $totalCatRev > 0 ? round(($catRow['total_revenue'] / $totalCatRev) * 100, 1) : 0;
                                $barPct  = $maxCatRev  > 0 ? round(($catRow['total_revenue'] / $maxCatRev) * 100) : 0;
                                $iconLabel = $iconLabels[$catRow['category_icon']] ?? '🏷️';
                            ?>
                            <tr class="hover:bg-slate-50">
                                <!-- Rank -->
                                <td class="p-3 pl-4 font-extrabold text-slate-400 text-[11px]"><?= $idx + 1 ?></td>
                                <!-- Category name + icon -->
                                <td class="p-3">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-sm flex-shrink-0"
                                              style="background:<?= $color['bg'] ?>; border:1px solid <?= $color['border'] ?>;">
                                            <?= $iconLabel ?>
                                        </span>
                                        <span class="font-bold text-slate-800"><?= e($catRow['category_name']) ?></span>
                                    </div>
                                </td>
                                <!-- Items -->
                                <td class="p-3 text-center font-semibold text-slate-600"><?= number_format((int)$catRow['items_sold']) ?></td>
                                <!-- Revenue -->
                                <td class="p-3 text-right font-extrabold" style="color:<?= $color['text'] ?>"><?= formatMoney($catRow['total_revenue']) ?></td>
                                <!-- Share % -->
                                <td class="p-3 text-right font-bold text-slate-500"><?= $share ?>%</td>
                                <!-- Bar -->
                                <td class="p-3">
                                    <div style="background:#F1F5F9; border-radius:99px; height:8px; width:100%; overflow:hidden;">
                                        <div style="height:8px; border-radius:99px; width:<?= $barPct ?>%; background:<?= $color['text'] ?>; min-width:4px;"></div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <!-- Totals footer row -->
                        <tfoot>
                            <tr class="bg-slate-100 border-t-2 border-slate-300">
                                <td class="p-3 pl-4" colspan="2">
                                    <span class="font-extrabold text-slate-700 text-[11px] uppercase tracking-wide">TOTAL</span>
                                </td>
                                <td class="p-3 text-center font-extrabold text-slate-700">
                                    <?= number_format(array_sum(array_column($revenueByCategory, 'items_sold'))) ?>
                                </td>
                                <td class="p-3 text-right font-extrabold text-emerald-700 text-sm"><?= formatMoney($totalCatRev) ?></td>
                                <td class="p-3 text-right font-extrabold text-slate-500">100%</td>
                                <td class="p-3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- Section 1: Compras de Materia Prima -->

            <div class="space-y-3 page-break-inside-avoid">
                <h3 class="text-sm font-heading font-extrabold uppercase tracking-wider text-emerald-950 flex items-center gap-2 border-b border-emerald-100 pb-2">
                    <span>🛒</span> Detalle de Compras de Materia Prima (Aumento de Stock)
                </h3>
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-emerald-50 text-emerald-950 font-bold uppercase tracking-wider text-[10px]">
                                <th class="p-3">Fecha</th>
                                <th class="p-3">Insumo / Ingrediente</th>
                                <th class="p-3 text-center">Cantidad</th>
                                <th class="p-3 text-right">P. Unitario</th>
                                <th class="p-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($compras)): ?>
                                <tr><td colspan="5" class="p-3 text-center text-slate-400 italic">No hay compras registradas en este período.</td></tr>
                            <?php else: ?>
                                <?php foreach ($compras as $c): ?>
                                    <tr class="hover:bg-slate-50">
                                        <td class="p-3 text-slate-600 font-medium"><?= date('d/m/Y', strtotime($c['fecha'])) ?></td>
                                        <td class="p-3 font-bold text-slate-800"><?= e($c['material_name']) ?></td>
                                        <td class="p-3 text-center font-semibold text-slate-700"><?= number_format($c['cantidad'], 2) ?> <?= e($c['unit']) ?></td>
                                        <td class="p-3 text-right text-slate-600"><?= formatMoney($c['precio_unitario']) ?></td>
                                        <td class="p-3 text-right font-extrabold text-emerald-700"><?= formatMoney($c['total']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Section 2: Gastos Fijos -->
            <div class="space-y-3 page-break-inside-avoid">
                <h3 class="text-sm font-heading font-extrabold uppercase tracking-wider text-rose-950 flex items-center gap-2 border-b border-rose-100 pb-2">
                    <span>📌</span> Detalle de Gastos Fijos
                </h3>
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-rose-50 text-rose-950 font-bold uppercase tracking-wider text-[10px]">
                                <th class="p-3">Fecha</th>
                                <th class="p-3">Nombre del Gasto Fijo</th>
                                <th class="p-3 text-right">Monto</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($gastosFijos)): ?>
                                <tr><td colspan="3" class="p-3 text-center text-slate-400 italic">No hay gastos fijos en este período.</td></tr>
                            <?php else: ?>
                                <?php foreach ($gastosFijos as $gf): ?>
                                    <tr class="hover:bg-slate-50">
                                        <td class="p-3 text-slate-600 font-medium"><?= date('d/m/Y', strtotime($gf['fecha'])) ?></td>
                                        <td class="p-3 font-bold text-slate-800"><?= e($gf['nombre']) ?></td>
                                        <td class="p-3 text-right font-extrabold text-rose-600"><?= formatMoney($gf['precio']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Section 3: Ventas Realizadas -->
            <div class="space-y-3 page-break-inside-avoid">
                <h3 class="text-sm font-heading font-extrabold uppercase tracking-wider text-coffee-dark flex items-center gap-2 border-b border-cream-dark pb-2">
                    <span>💵</span> Detalle de Ventas Registradas
                </h3>
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-100 text-slate-700 font-bold uppercase tracking-wider text-[10px]">
                                <th class="p-3">ID Venta</th>
                                <th class="p-3">Fecha y Hora</th>
                                <th class="p-3">Cajero</th>
                                <th class="p-3 text-center">Tipo Pago</th>
                                <th class="p-3 text-center">Items</th>
                                <th class="p-3 text-right">Total Venta</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($sales)): ?>
                                <tr><td colspan="6" class="p-3 text-center text-slate-400 italic">No se registraron ventas en este período.</td></tr>
                            <?php else: ?>
                                <?php foreach ($sales as $sale): ?>
                                    <?php 
                                    $pm = strtolower($sale['payment_method'] ?? '');
                                    $isQr = ($pm === 'qr');
                                    $isEf = ($pm === 'efectivo');
                                    $isUnspecified = empty($pm);
                                    ?>
                                    <tr class="hover:bg-slate-50">
                                        <td class="p-3 font-bold text-coffee-dark">#<?= $sale['id'] ?></td>
                                        <td class="p-3 text-slate-600"><?= date('d/m/Y H:i', strtotime($sale['sale_date'])) ?></td>
                                        <td class="p-3 text-slate-700 font-medium"><?= e($sale['cashier_name'] ?? 'Caja') ?></td>
                                        <td class="p-3 text-center font-bold">
                                            <?php if ($isUnspecified): ?>
                                                <span class="text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200 text-[10px]">⚠️ Sin Especificar</span>
                                            <?php else: ?>
                                                <span class="<?= $isQr ? 'text-blue-700' : 'text-emerald-700' ?>">
                                                    <?= $isQr ? '📱 QR' : '💵 Efectivo' ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="p-3 text-center font-bold text-slate-700"><?= $sale['items_count'] ?></td>
                                        <td class="p-3 text-right font-extrabold text-emerald-700"><?= formatMoney($sale['total']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Historical Prices Section in Print View -->
            <?php if (!empty($priceRangeReport)): ?>
            <div class="space-y-3 page-break-inside-avoid">
                <h3 class="text-sm font-heading font-extrabold uppercase tracking-wider text-slate-800 flex items-center gap-2 border-b-2 border-amber-300 pb-2">
                    <span style="display:inline-block;width:1.1rem;height:1.1rem;vertical-align:middle;">💰</span>
                    Análisis de Precios Históricos por Producto
                </h3>

                <div class="space-y-4">
                    <?php foreach ($priceRangeReport as $prodData): ?>
                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                        <div class="bg-amber-50/50 px-3 py-2 border-b border-slate-200 flex items-center justify-between">
                            <span class="font-bold text-xs text-slate-800"><?= e($prodData['product_icon']) ?> <?= e($prodData['product_name']) ?></span>
                            <span class="text-[10px] font-bold text-amber-700 bg-white border border-amber-200 px-2 py-0.5 rounded">
                                <?= count($prodData['prices']) ?> precios distintos
                            </span>
                        </div>
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-slate-700 font-bold uppercase text-[9px]">
                                    <th class="p-2 pl-3">Precio Unitario</th>
                                    <th class="p-2">Vigente Desde</th>
                                    <th class="p-2">Vigente Hasta</th>
                                    <th class="p-2 text-center">Unidades Vendidas</th>
                                    <th class="p-2 pr-3 text-right">Subtotal Recaudado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php foreach ($prodData['prices'] as $pr): 
                                    $vDesde = !empty($pr['vigencia_desde']) ? $pr['vigencia_desde'] : $pr['date_from'];
                                    $vHasta = !empty($pr['vigencia_hasta']) ? $pr['vigencia_hasta'] : $pr['date_to'];
                                ?>
                                <tr>
                                    <td class="p-2 pl-3 font-extrabold text-emerald-700"><?= formatMoney($pr['price']) ?> c/u</td>
                                    <td class="p-2 text-slate-600 font-medium"><?= date('d/m/Y', strtotime($vDesde)) ?></td>
                                    <td class="p-2 text-slate-600 font-medium"><?= date('d/m/Y', strtotime($vHasta)) ?></td>
                                    <td class="p-2 text-center font-semibold text-slate-700"><?= number_format($pr['qty']) ?> unidades</td>
                                    <td class="p-2 pr-3 text-right font-extrabold text-slate-800"><?= formatMoney($pr['total']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Footer Signatures & Disclaimer -->
            <div class="pt-8 border-t border-slate-200 flex flex-col sm:flex-row justify-between items-end gap-6 text-xs text-slate-400 page-break-inside-avoid">
                <div>
                    <p class="font-semibold text-slate-500">Duke's Fast Food POS</p>
                    <p class="text-[11px]">Documento generado automáticamente para el control interno de caja y balance.</p>
                </div>
                <div class="flex gap-8 text-center pt-4 sm:pt-0">
                    <div class="w-36 border-t border-slate-400 pt-1">
                        <p class="font-bold text-slate-600 text-[11px]">Firma Administrador</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
