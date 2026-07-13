<?php
// Prepare chart data for Javascript
$labels = [];
$values = [];
foreach ($chartSales as $row) {
    $labels[] = date('d M', strtotime($row['date']));
    $values[] = (float)$row['total'];
}

$topProdLabels = [];
$topProdValues = [];
foreach ($topProducts as $row) {
    $topProdLabels[] = $row['name'];
    $topProdValues[] = (int)$row['qty'];
}
?>

<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 bg-white p-6 rounded-2xl border border-cream-dark shadow-sm">
        <div>
            <h1 class="text-3xl font-heading font-extrabold text-coffee-dark">Panel de Control</h1>
            <p class="text-coffee-light">Estadísticas de ventas y alertas de stock de Duke's Fast Food</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2 bg-cream border border-cream-dark px-3.5 py-2 rounded-xl shadow-inner">
                <label for="dashboard-date" class="text-xs font-bold text-coffee-medium uppercase select-none">Fecha:</label>
                <input type="date" id="dashboard-date" value="<?= e($selectedDate) ?>" onchange="window.location.href = '<?= BASE_URL ?>/?date=' + this.value"
                       class="bg-transparent border-0 text-sm font-bold text-coffee-dark focus:ring-0 focus:outline-none cursor-pointer">
            </div>
            <a href="<?= BASE_URL ?>/sales/daily-report?date=<?= e($selectedDate) ?>" target="_blank"
               class="inline-flex items-center gap-2 bg-coffee-dark hover:bg-coffee-medium text-white px-5 py-2.5 rounded-xl font-bold shadow-md transition duration-200 active:scale-95 text-sm">
                🖨️ Informe del Día
            </a>
            <a href="<?= BASE_URL ?>/pos" class="inline-flex items-center gap-2 bg-accent hover:bg-accent-dark text-white px-5 py-2.5 rounded-xl font-bold shadow-md transition duration-200 active:scale-95 text-sm">
                🛒 Abrir POS
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Day Card -->
        <div class="bg-white p-6 rounded-2xl border border-cream-dark shadow-sm flex items-center justify-between hover-card animate-fade-in">
            <div class="space-y-1">
                <span class="text-xs font-bold text-coffee-medium tracking-wider uppercase">Ventas del Día</span>
                <h3 class="text-2xl font-extrabold text-coffee-dark"><?= formatMoney($stats['day_revenue']) ?></h3>
                <span class="text-xs text-slate-500 font-medium"><?= (int)$stats['day_count'] ?> transacciones · <?= date('d/m/Y', strtotime($selectedDate)) ?></span>
            </div>
            <div class="p-4 bg-amber-50 text-amber-600 rounded-2xl text-2xl transition duration-300 hover:rotate-12">🍔</div>
        </div>

        <!-- Week Card -->
        <div class="bg-white p-6 rounded-2xl border border-cream-dark shadow-sm flex items-center justify-between hover-card animate-fade-in" style="animation-delay: 0.1s;">
            <div class="space-y-1">
                <span class="text-xs font-bold text-coffee-medium tracking-wider uppercase">Últimos 7 Días</span>
                <h3 class="text-2xl font-extrabold text-coffee-dark"><?= formatMoney($stats['week_revenue']) ?></h3>
                <span class="text-xs text-slate-500 font-medium"><?= (int)$stats['week_count'] ?> transacciones</span>
            </div>
            <div class="p-4 bg-orange-50 text-accent rounded-2xl text-2xl transition duration-300 hover:scale-110">📈</div>
        </div>

        <!-- Month Card -->
        <div class="bg-white p-6 rounded-2xl border border-cream-dark shadow-sm flex items-center justify-between hover-card animate-fade-in" style="animation-delay: 0.2s;">
            <div class="space-y-1">
                <span class="text-xs font-bold text-coffee-medium tracking-wider uppercase">Mes Actual</span>
                <h3 class="text-2xl font-extrabold text-coffee-dark"><?= formatMoney($stats['month_revenue']) ?></h3>
                <span class="text-xs text-slate-500 font-medium"><?= (int)$stats['month_count'] ?> transacciones</span>
            </div>
            <div class="p-4 bg-emerald-50 text-emerald-600 rounded-2xl text-2xl transition duration-300 hover:-translate-y-1">💰</div>
        </div>
    </div>

    <!-- Alert Banner for Low Stock -->
    <?php if (!empty($lowStockMaterials)): ?>
        <div class="bg-amber-50 border border-amber-200 p-6 rounded-2xl shadow-sm">
            <div class="flex items-start gap-3">
                <span class="text-2xl">⚠️</span>
                <div class="space-y-2 w-full">
                    <h4 class="font-bold text-amber-900">Materia Prima en Nivel Crítico</h4>
                    <p class="text-sm text-amber-700">Los siguientes ingredientes están por debajo de su stock mínimo configurado.</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 pt-2">
                        <?php foreach ($lowStockMaterials as $mat): ?>
                            <div class="bg-white border border-amber-200 px-4 py-2 rounded-xl flex items-center justify-between text-xs">
                                <span class="font-semibold text-coffee-dark"><?= e($mat['name']) ?></span>
                                <span class="font-bold text-rose-600"><?= number_format($mat['current_stock'], 1) ?> / <?= number_format($mat['min_stock'], 1) ?> <?= e($mat['unit']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Data Tables + Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Sales Trend Chart (2 cols) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-cream-dark shadow-sm space-y-4 hover-card animate-fade-in" style="animation-delay: 0.3s;">
            <h3 class="font-heading font-bold text-lg text-coffee-dark">📉 Tendencia (7 Días hasta <?= date('d/m/Y', strtotime($selectedDate)) ?>)</h3>
            <?php if (empty($chartSales)): ?>
                <div class="h-56 flex flex-col items-center justify-center text-slate-400 text-sm gap-2">
                    <span class="text-3xl">📊</span>
                    <span>Sin datos para este periodo.</span>
                </div>
            <?php else: ?>
            <!-- Mini summary table above chart -->
            <div class="grid grid-cols-3 gap-3 pb-2">
                <?php
                $chartTotal = array_sum(array_column($chartSales, 'total'));
                $chartDays  = count($chartSales);
                $chartAvg   = $chartDays > 0 ? $chartTotal / $chartDays : 0;
                $chartMax   = !empty($chartSales) ? max(array_column($chartSales, 'total')) : 0;
                ?>
                <div class="bg-cream rounded-xl p-3 text-center">
                    <div class="text-[10px] font-bold text-coffee-medium uppercase tracking-wider">Total 7 días</div>
                    <div class="text-base font-extrabold text-coffee-dark mt-0.5"><?= formatMoney($chartTotal) ?></div>
                </div>
                <div class="bg-cream rounded-xl p-3 text-center">
                    <div class="text-[10px] font-bold text-coffee-medium uppercase tracking-wider">Promedio/día</div>
                    <div class="text-base font-extrabold text-coffee-dark mt-0.5"><?= formatMoney($chartAvg) ?></div>
                </div>
                <div class="bg-cream rounded-xl p-3 text-center">
                    <div class="text-[10px] font-bold text-coffee-medium uppercase tracking-wider">Mejor día</div>
                    <div class="text-base font-extrabold text-coffee-dark mt-0.5"><?= formatMoney($chartMax) ?></div>
                </div>
            </div>
            <div class="relative h-52">
                <canvas id="salesChart"></canvas>
            </div>
            <?php endif; ?>
        </div>

        <!-- Top Products (readable table) -->
        <div class="bg-white p-6 rounded-2xl border border-cream-dark shadow-sm space-y-4 hover-card animate-fade-in" style="animation-delay: 0.4s;">
            <div class="flex items-center justify-between">
                <h3 class="font-heading font-bold text-lg text-coffee-dark">🏆 Más Vendidos</h3>
                <span class="text-xs text-slate-400"><?= date('d/m/Y', strtotime($selectedDate)) ?></span>
            </div>
            <?php if (empty($topProducts)): ?>
                <div class="h-52 flex flex-col items-center justify-center text-slate-400 text-sm gap-2">
                    <span class="text-3xl">🍟</span>
                    <span>Sin ventas este día.</span>
                </div>
            <?php else: ?>
                <div class="space-y-2">
                    <?php
                    $maxQty = max(array_column($topProducts, 'qty'));
                    $colors = ['bg-amber-500', 'bg-orange-400', 'bg-amber-400', 'bg-yellow-400', 'bg-lime-400'];
                    foreach ($topProducts as $i => $prod):
                        $pct = $maxQty > 0 ? round((int)$prod['qty'] / $maxQty * 100) : 0;
                        $color = $colors[$i] ?? 'bg-slate-300';
                    ?>
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-2">
                                <span class="w-5 h-5 flex items-center justify-center rounded-full bg-coffee-dark text-white text-[10px] font-bold"><?= $i+1 ?></span>
                                <span class="font-semibold text-coffee-dark truncate max-w-[130px]"><?= e($prod['name']) ?></span>
                            </div>
                            <span class="font-bold text-coffee-medium text-xs"><?= (int)$prod['qty'] ?> unid.</span>
                        </div>
                        <div class="w-full bg-cream rounded-full h-2">
                            <div class="<?= $color ?> h-2 rounded-full transition-all duration-700" style="width: <?= $pct ?>%"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Totals row -->
                <div class="pt-3 border-t border-cream-dark flex items-center justify-between text-xs text-slate-500">
                    <span><?= count($topProducts) ?> productos distintos</span>
                    <span><?= array_sum(array_column($topProducts, 'qty')) ?> unidades total</span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bottom: Detailed Day Sales Table -->
    <?php if (!empty($topProducts)): ?>
    <div class="bg-white rounded-2xl border border-cream-dark shadow-sm overflow-hidden hover-card animate-fade-in" style="animation-delay: 0.5s;">
        <div class="p-5 border-b border-cream-dark flex items-center justify-between">
            <div>
                <h3 class="font-heading font-bold text-lg text-coffee-dark">📋 Tabla de Ventas — <?= date('d/m/Y', strtotime($selectedDate)) ?></h3>
                <p class="text-xs text-slate-400 mt-0.5">Detalle de productos vendidos con ingresos calculados</p>
            </div>
            <a href="<?= BASE_URL ?>/sales/daily-report?date=<?= e($selectedDate) ?>" target="_blank"
               class="flex items-center gap-2 bg-coffee-dark hover:bg-coffee-medium text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-all active:scale-95">
                🖨️ Ver Informe Completo
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-coffee-dark text-white">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider">Posición</th>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider">Producto</th>
                        <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wider">Unidades</th>
                        <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wider">Participación</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cream-dark">
                    <?php
                    $grandTotal = array_sum(array_column($topProducts, 'qty'));
                    foreach ($topProducts as $i => $prod):
                        $share = $grandTotal > 0 ? round((int)$prod['qty'] / $grandTotal * 100, 1) : 0;
                        $medals = ['🥇', '🥈', '🥉'];
                        $medal  = $medals[$i] ?? '#'.($i+1);
                    ?>
                    <tr class="hover:bg-cream/50 transition">
                        <td class="px-5 py-3.5 font-bold text-coffee-dark"><?= $medal ?></td>
                        <td class="px-5 py-3.5 font-semibold text-coffee-dark"><?= e($prod['name']) ?></td>
                        <td class="px-5 py-3.5 text-right font-bold text-coffee-medium"><?= (int)$prod['qty'] ?> unid.</td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <div class="w-20 bg-cream rounded-full h-1.5">
                                    <div class="bg-accent h-1.5 rounded-full" style="width:<?= $share ?>%"></div>
                                </div>
                                <span class="text-xs font-bold text-slate-500 w-10 text-right"><?= $share ?>%</span>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-cream">
                    <tr>
                        <td colspan="2" class="px-5 py-3 font-bold text-coffee-dark text-sm">TOTAL</td>
                        <td class="px-5 py-3 text-right font-extrabold text-coffee-dark"><?= $grandTotal ?> unid.</td>
                        <td class="px-5 py-3 text-right font-extrabold text-coffee-dark">100%</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <?php endif; ?>

</div>


<!-- ChartJS Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Sales Trend Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                label: 'Ingresos ($)',
                data: <?= json_encode($values) ?>,
                borderColor: '#E07B39',
                backgroundColor: 'rgba(224, 123, 57, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.3,
                pointBackgroundColor: '#3D1C02',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#F5E6D3' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // Top Products Chart
    <?php if (!empty($topProducts)): ?>
    const prodCtx = document.getElementById('productsChart').getContext('2d');
    new Chart(prodCtx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($topProdLabels) ?>,
            datasets: [{
                data: <?= json_encode($topProdValues) ?>,
                backgroundColor: [
                    '#3D1C02', // coffee-dark
                    '#E07B39', // accent orange
                    '#7B4F2E', // coffee-medium
                    '#A0714F', // coffee-light
                    '#2D6A4F'  // forest green
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, font: { size: 11 } }
                }
            }
        }
    });
    <?php endif; ?>
</script>
