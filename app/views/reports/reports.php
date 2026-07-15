<div class="space-y-8 animate-slide-up">
    <!-- Header -->
    <div class="bg-white p-6 rounded-2xl border border-cream-dark shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-heading font-extrabold text-coffee-dark">📊 Informe General de Balance</h1>
            <p class="text-coffee-light mt-1">Consulta los ingresos de ventas versus los gastos administrativos registrados en el restaurante.</p>
        </div>
        <div>
            <a href="<?= BASE_URL ?>/reports/print?filter_mode=<?= e($filterMode) ?>&date=<?= isset($_GET['date']) ? e($_GET['date']) : date('Y-m-d') ?>&start_date=<?= e($startDate) ?>&end_date=<?= e($endDate) ?>" target="_blank"
               class="inline-flex items-center gap-2 bg-coffee-dark hover:bg-coffee-medium text-white px-5 py-2.5 rounded-xl font-bold shadow-md transition duration-200 active:scale-95 text-sm">
                🖨️ Imprimir PDF / A4
            </a>
        </div>
    </div>

    <!-- Period Filters -->
    <div class="bg-white p-6 rounded-2xl border border-cream-dark shadow-sm space-y-4">
        <h2 class="text-xs font-bold text-coffee-medium uppercase tracking-wider">Filtrar período del informe</h2>
        <div class="flex flex-wrap items-center gap-4">
            <!-- Day Button Link -->
            <a href="<?= BASE_URL ?>/reports?filter_mode=day&date=<?= date('Y-m-d') ?>" 
               class="px-4 py-2 text-xs font-bold rounded-xl transition duration-200 <?= $filterMode === 'day' && !isset($_GET['date']) || (isset($_GET['date']) && $_GET['date'] === date('Y-m-d')) ? 'bg-coffee-dark text-white' : 'bg-cream border border-cream-dark text-coffee-medium hover:bg-cream-dark/55' ?>">
                Hoy
            </a>
            <!-- Weekly Link -->
            <a href="<?= BASE_URL ?>/reports?filter_mode=week" 
               class="px-4 py-2 text-xs font-bold rounded-xl transition duration-200 <?= $filterMode === 'week' ? 'bg-coffee-dark text-white' : 'bg-cream border border-cream-dark text-coffee-medium hover:bg-cream-dark/55' ?>">
                Esta Semana
            </a>
            
            <form method="GET" action="<?= BASE_URL ?>/reports" class="flex flex-wrap items-center gap-3 ml-0 md:ml-auto">
                <input type="hidden" name="filter_mode" value="range">
                <div class="flex items-center gap-2">
                    <label for="start_date" class="text-xs font-bold text-coffee-medium uppercase">Desde:</label>
                    <input type="date" id="start_date" name="start_date" value="<?= e($startDate) ?>"
                           class="px-3 py-1.5 rounded-xl border border-cream-dark text-xs focus:outline-none focus:ring-2 focus:ring-accent">
                </div>
                <div class="flex items-center gap-2">
                    <label for="end_date" class="text-xs font-bold text-coffee-medium uppercase">Hasta:</label>
                    <input type="date" id="end_date" name="end_date" value="<?= e($endDate) ?>"
                           class="px-3 py-1.5 rounded-xl border border-cream-dark text-xs focus:outline-none focus:ring-2 focus:ring-accent">
                </div>
                <button type="submit" class="bg-accent hover:bg-accent-dark text-white font-bold px-4 py-2 rounded-xl text-xs transition shadow-sm">
                    Filtrar Rango
                </button>
            </form>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Revenue Card -->
        <div class="bg-white p-6 rounded-2xl border border-cream-dark shadow-sm flex items-center justify-between hover-card">
            <div class="space-y-1">
                <span class="text-xs font-bold text-emerald-700 tracking-wider uppercase">Ingresos Totales (Ventas)</span>
                <h3 class="text-3xl font-extrabold text-emerald-800"><?= formatMoney($totalRevenue) ?></h3>
                <span class="text-xs text-slate-500 font-medium"><?= count($sales) ?> ventas concretadas</span>
            </div>
            <div class="p-4 bg-emerald-50 text-emerald-600 rounded-2xl text-2xl">📈</div>
        </div>

        <!-- Expenses Card -->
        <div class="bg-white p-6 rounded-2xl border border-cream-dark shadow-sm flex items-center justify-between hover-card">
            <div class="space-y-1">
                <span class="text-xs font-bold text-rose-700 tracking-wider uppercase">Gastos Administrativos</span>
                <h3 class="text-3xl font-extrabold text-rose-800"><?= formatMoney($totalExpenses) ?></h3>
                <span class="text-xs text-slate-500 font-medium"><?= count($expenses) ?> registros de egreso</span>
            </div>
            <div class="p-4 bg-rose-50 text-rose-600 rounded-2xl text-2xl">📉</div>
        </div>

        <!-- Net Total Card -->
        <div class="bg-white p-6 rounded-2xl border border-cream-dark shadow-sm flex items-center justify-between hover-card">
            <div class="space-y-1">
                <span class="text-xs font-bold text-coffee-medium tracking-wider uppercase">Total Balance (Neto)</span>
                <h3 class="text-3xl font-extrabold <?= $totalNet >= 0 ? 'text-coffee-dark' : 'text-rose-600' ?>">
                    <?= formatMoney($totalNet) ?>
                </h3>
                <span class="text-xs text-slate-500 font-medium">Ingresos - Gastos</span>
            </div>
            <div class="p-4 rounded-2xl text-2xl <?= $totalNet >= 0 ? 'bg-amber-50 text-amber-600' : 'bg-rose-50 text-rose-600' ?>">
                💰
            </div>
        </div>
    </div>

    <!-- Details Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Sales list (Left) -->
        <div class="bg-white rounded-2xl border border-cream-dark shadow-sm overflow-hidden flex flex-col">
            <div class="p-5 border-b border-cream-dark bg-emerald-50/50 flex justify-between items-center">
                <h3 class="font-heading font-extrabold text-base text-emerald-950 flex items-center gap-2">
                    <span>💵</span> Detalle de Ingresos (Ventas)
                </h3>
            </div>
            <div class="flex-grow overflow-y-auto max-h-[500px] custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-cream text-coffee-medium border-b border-cream-dark text-xs uppercase font-bold tracking-wider">
                            <th class="p-4 pl-6">ID Venta</th>
                            <th class="p-4">Fecha</th>
                            <th class="p-4 text-center">Items</th>
                            <th class="p-4 pr-6 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-cream-dark text-sm">
                        <?php if (empty($sales)): ?>
                            <tr>
                                <td colspan="4" class="p-6 text-center text-slate-400">No se registraron ventas en este período.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($sales as $sale): ?>
                                <tr class="hover:bg-cream/10 transition">
                                    <td class="p-4 pl-6 font-bold text-coffee-dark">#<?= $sale['id'] ?></td>
                                    <td class="p-4 text-slate-500 font-medium"><?= date('d/m/Y H:i', strtotime($sale['sale_date'])) ?></td>
                                    <td class="p-4 text-center text-coffee-medium font-semibold"><?= $sale['items_count'] ?></td>
                                    <td class="p-4 pr-6 text-right font-extrabold text-emerald-700"><?= formatMoney($sale['total']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Expenses list (Right) -->
        <div class="bg-white rounded-2xl border border-cream-dark shadow-sm overflow-hidden flex flex-col">
            <div class="p-5 border-b border-cream-dark bg-rose-50/50 flex justify-between items-center">
                <h3 class="font-heading font-extrabold text-base text-rose-950 flex items-center gap-2">
                    <span>💸</span> Detalle de Egresos (Gastos)
                </h3>
            </div>
            <div class="flex-grow overflow-y-auto max-h-[500px] custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-cream text-coffee-medium border-b border-cream-dark text-xs uppercase font-bold tracking-wider">
                            <th class="p-4 pl-6">Fecha</th>
                            <th class="p-4">Producto / Detalle</th>
                            <th class="p-4 text-center">Cant.</th>
                            <th class="p-4 pr-6 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-cream-dark text-sm">
                        <?php if (empty($expenses)): ?>
                            <tr>
                                <td colspan="4" class="p-6 text-center text-slate-400">No se registraron gastos en este período.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($expenses as $exp): ?>
                                <tr class="hover:bg-cream/10 transition">
                                    <td class="p-4 pl-6 text-slate-500 font-medium"><?= date('d/m/Y', strtotime($exp['fecha'])) ?></td>
                                    <td class="p-4 font-bold text-coffee-dark"><?= e($exp['producto_nombre']) ?></td>
                                    <td class="p-4 text-center text-coffee-medium font-semibold"><?= $exp['cantidad'] ?></td>
                                    <td class="p-4 pr-6 text-right font-extrabold text-rose-600"><?= formatMoney($exp['total']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
