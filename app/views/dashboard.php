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
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-heading font-extrabold text-coffee-dark">Panel de Control</h1>
            <p class="text-coffee-light">Estadísticas de ventas y alertas de stock de Duke's Cakes</p>
        </div>
        <div>
            <a href="<?= BASE_URL ?>/pos" class="inline-flex items-center gap-2 bg-accent hover:bg-accent-dark text-white px-5 py-3 rounded-xl font-bold shadow-md transition duration-200">
                <span>🛒</span> Abrir Caja / POS
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
                <span class="text-xs text-slate-500 font-medium"><?= (int)$stats['day_count'] ?> transacciones</span>
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
                    <p class="text-sm text-amber-700">Los siguientes ingredientes están por debajo de su stock mínimo configurado. ¡Es necesario reabastecer!</p>
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

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sales Trend Line Chart -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-cream-dark shadow-sm space-y-4 hover-card animate-fade-in" style="animation-delay: 0.3s;">
            <h3 class="font-heading font-bold text-lg text-coffee-dark">Tendencia de Ventas (Últimos 7 Días)</h3>
            <div class="relative h-64">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Top Products Bar Chart -->
        <div class="bg-white p-6 rounded-2xl border border-cream-dark shadow-sm space-y-4 hover-card animate-fade-in" style="animation-delay: 0.4s;">
            <h3 class="font-heading font-bold text-lg text-coffee-dark">Productos Más Vendidos</h3>
            <?php if (empty($topProducts)): ?>
                <div class="h-64 flex flex-col items-center justify-center text-slate-400 text-sm">
                    <span>🍟</span>
                    <span>No hay datos de ventas aún.</span>
                </div>
            <?php else: ?>
                <div class="relative h-64">
                    <canvas id="productsChart"></canvas>
                </div>
            <?php endif; ?>
        </div>
    </div>
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
