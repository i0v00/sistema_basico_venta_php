<div class="space-y-8 animate-slide-up">
    <!-- Header -->
    <div class="bg-white p-6 rounded-2xl border border-cream-dark shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-heading font-extrabold text-coffee-dark">📊 Informe General de Balance</h1>
            <p class="text-coffee-light mt-1 font-medium">
                Período: <span class="font-extrabold text-coffee-dark bg-cream border border-cream-dark px-2.5 py-0.5 rounded-lg">Del <?= date('d/m/Y', strtotime($startDate)) ?> al <?= date('d/m/Y', strtotime($endDate)) ?></span>
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="<?= BASE_URL ?>/reports/print?filter_mode=<?= e($filterMode) ?>&date=<?= isset($_GET['date']) ? e($_GET['date']) : date('Y-m-d') ?>&month=<?= e($selectedMonth ?? date('m')) ?>&year=<?= e($selectedYear ?? date('Y')) ?>&start_date=<?= e($startDate) ?>&end_date=<?= e($endDate) ?>" target="_blank"
               class="inline-flex items-center gap-2 bg-coffee-dark hover:bg-coffee-medium text-white px-5 py-3 rounded-xl font-extrabold shadow-md transition duration-200 active:scale-95 text-sm">
                🖨️ Imprimir PDF / A4
            </a>
            <!-- Backup DB Button -->
            <a href="<?= BASE_URL ?>/reports/backup"
               id="btn-backup-db"
               onclick="return confirmBackup(this)"
               class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-3 rounded-xl font-extrabold shadow-md transition duration-200 active:scale-95 text-sm">
                💾 Backup BD
            </a>
        </div>
        <script>
        function confirmBackup(el) {
            if (!confirm('¿Deseas descargar un backup completo de la base de datos?\n\nEsto generará un archivo .sql con todos los datos actuales.')) {
                return false;
            }
            el.textContent = '⏳ Generando...';
            el.classList.add('opacity-70', 'pointer-events-none');
            // Re-enable after 5 s in case user stays on page
            setTimeout(function() {
                el.innerHTML = '💾 Backup BD';
                el.classList.remove('opacity-70', 'pointer-events-none');
            }, 5000);
            return true;
        }
        </script>
    </div>

    <!-- Period Filters Section -->
    <div class="bg-white p-6 rounded-2xl border border-cream-dark shadow-sm space-y-5">
        
        <!-- PROMINENT MONTHLY SELECTOR BOX -->
        <div class="bg-gradient-to-r from-coffee-dark to-coffee-medium p-5 rounded-2xl text-white shadow-md space-y-3">
            <div class="flex items-center gap-2">
                <span class="text-2xl">🗓️</span>
                <div>
                    <h2 class="font-heading font-extrabold text-base tracking-wide">Reporte Mensual Completo (Del día 1 al final de mes)</h2>
                    <p class="text-white/70 text-xs">Selecciona el mes y año para calcular automáticamente todos los ingresos y egresos del mes entero.</p>
                </div>
            </div>
            
            <form method="GET" action="<?= BASE_URL ?>/reports" class="flex flex-wrap items-center gap-3 pt-1">
                <input type="hidden" name="filter_mode" value="month">
                
                <div class="flex items-center gap-2">
                    <label class="text-xs font-bold text-cream-dark uppercase">Mes:</label>
                    <select name="month" class="px-4 py-2.5 rounded-xl border border-white/20 text-sm font-extrabold text-coffee-dark bg-white focus:outline-none focus:ring-2 focus:ring-accent cursor-pointer shadow-sm">
                        <?php
                        $meses = [
                            '01' => 'Enero (1 al 31)', '02' => 'Febrero (1 al 28/29)', '03' => 'Marzo (1 al 31)', '04' => 'Abril (1 al 30)',
                            '05' => 'Mayo (1 al 31)', '06' => 'Junio (1 al 30)', '07' => 'Julio (1 al 31)', '08' => 'Agosto (1 al 31)',
                            '09' => 'Septiembre (1 al 30)', '10' => 'Octubre (1 al 31)', '11' => 'Noviembre (1 al 30)', '12' => 'Diciembre (1 al 31)'
                        ];
                        $currentM = $selectedMonth ?? date('m');
                        foreach ($meses as $num => $nombre):
                        ?>
                            <option value="<?= $num ?>" <?= $num === $currentM ? 'selected' : '' ?>><?= $nombre ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <label class="text-xs font-bold text-cream-dark uppercase">Año:</label>
                    <select name="year" class="px-4 py-2.5 rounded-xl border border-white/20 text-sm font-extrabold text-coffee-dark bg-white focus:outline-none focus:ring-2 focus:ring-accent cursor-pointer shadow-sm">
                        <?php
                        $currentY = (int)($selectedYear ?? date('Y'));
                        for ($y = date('Y') - 3; $y <= date('Y') + 1; $y++):
                        ?>
                            <option value="<?= $y ?>" <?= $y === $currentY ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <!-- BIGGER & HIGHLIGHTED VER MES BUTTON -->
                <button type="submit" class="bg-accent hover:bg-accent-dark text-white px-6 py-2.5 rounded-xl text-sm font-extrabold transition shadow-lg shadow-accent/30 active:scale-95 flex items-center gap-2">
                    <span>🗓️</span> GENERAR INFORME DEL MES
                </button>
            </form>
        </div>

        <!-- Quick Secondary Filters (Hoy, Esta Semana, Rango) -->
        <div class="flex flex-wrap items-center gap-3 pt-2">
            <span class="text-xs font-bold text-coffee-medium uppercase tracking-wider">Otros Filtros:</span>
            <!-- Day Button Link -->
            <a href="<?= BASE_URL ?>/reports?filter_mode=day&date=<?= date('Y-m-d') ?>" 
               class="px-4 py-2 text-xs font-bold rounded-xl transition duration-200 <?= $filterMode === 'day' && (!isset($_GET['date']) || $_GET['date'] === date('Y-m-d')) ? 'bg-coffee-dark text-white' : 'bg-cream border border-cream-dark text-coffee-medium hover:bg-cream-dark/55' ?>">
                Hoy
            </a>
            <!-- Weekly Link -->
            <a href="<?= BASE_URL ?>/reports?filter_mode=week" 
               class="px-4 py-2 text-xs font-bold rounded-xl transition duration-200 <?= $filterMode === 'week' ? 'bg-coffee-dark text-white' : 'bg-cream border border-cream-dark text-coffee-medium hover:bg-cream-dark/55' ?>">
                Esta Semana
            </a>
            
            <!-- Custom Range Form -->
            <form method="GET" action="<?= BASE_URL ?>/reports" class="flex flex-wrap items-center gap-2 ml-0 md:ml-auto">
                <input type="hidden" name="filter_mode" value="range">
                <div class="flex items-center gap-1.5">
                    <label for="start_date" class="text-xs font-bold text-coffee-medium uppercase">Desde:</label>
                    <input type="date" id="start_date" name="start_date" value="<?= e($startDate) ?>"
                           class="px-2.5 py-1.5 rounded-xl border border-cream-dark text-xs focus:outline-none focus:ring-2 focus:ring-accent">
                </div>
                <div class="flex items-center gap-1.5">
                    <label for="end_date" class="text-xs font-bold text-coffee-medium uppercase">Hasta:</label>
                    <input type="date" id="end_date" name="end_date" value="<?= e($endDate) ?>"
                           class="px-2.5 py-1.5 rounded-xl border border-cream-dark text-xs focus:outline-none focus:ring-2 focus:ring-accent">
                </div>
                <button type="submit" class="bg-coffee-dark hover:bg-coffee-medium text-white font-bold px-3.5 py-1.5 rounded-xl text-xs transition shadow-sm">
                    Filtrar Rango
                </button>
            </form>
        </div>
    </div>

    <!-- MAIN REVENUE & BALANCE METRICS GRID -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- 1. Total Revenue (Ingresos Combinados) -->
        <div class="bg-white p-6 rounded-2xl border border-cream-dark shadow-sm flex items-center justify-between hover-card">
            <div class="space-y-1">
                <span class="text-xs font-bold text-emerald-700 tracking-wider uppercase">Ingresos Totales (Efectivo + QR)</span>
                <h3 class="text-3xl font-extrabold text-emerald-800"><?= formatMoney($totalRevenue) ?></h3>
                <span class="text-xs text-slate-500 font-medium"><?= count($sales) ?> ventas en el período</span>
            </div>
            <div class="p-4 bg-emerald-50 text-emerald-600 rounded-2xl text-2xl font-bold">📈</div>
        </div>

        <!-- 2. Expenses Card -->
        <div class="bg-white p-6 rounded-2xl border border-cream-dark shadow-sm flex items-center justify-between hover-card">
            <div class="space-y-1">
                <span class="text-xs font-bold text-rose-700 tracking-wider uppercase">Egresos Totales</span>
                <h3 class="text-3xl font-extrabold text-rose-800"><?= formatMoney($totalExpenses) ?></h3>
                <span class="text-xs text-slate-500 font-medium">Compras + Gastos Fijos</span>
            </div>
            <div class="p-4 bg-rose-50 text-rose-600 rounded-2xl text-2xl font-bold">📉</div>
        </div>

        <!-- 3. Net Total Card -->
        <div class="bg-white p-6 rounded-2xl border border-cream-dark shadow-sm flex items-center justify-between hover-card">
            <div class="space-y-1">
                <span class="text-xs font-bold text-coffee-medium tracking-wider uppercase">Ganancia Limpia / Balance Neto</span>
                <h3 class="text-3xl font-extrabold <?= $totalNet >= 0 ? 'text-emerald-600' : 'text-rose-600' ?>">
                    <?= formatMoney($totalNet) ?>
                </h3>
                <span class="text-xs text-slate-500 font-medium">Total Ingresos - Total Egresos</span>
            </div>
            <div class="p-4 rounded-2xl text-2xl <?= $totalNet >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' ?>">
                💰
            </div>
        </div>
    </div>

    <!-- REVENUE BREAKDOWN BY PAYMENT METHOD (EFECTIVO vs QR) -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Ventas Efectivo -->
        <div class="bg-white p-5 rounded-2xl border-2 border-emerald-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider">💵 Ganado por Efectivo</span>
                <h4 class="text-2xl font-extrabold text-emerald-700 mt-1"><?= formatMoney($revenueByPayment['efectivo'] ?? 0) ?></h4>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5"><?= $countEfectivo ?> ventas en efectivo</p>
            </div>
            <span class="p-3 bg-emerald-50 text-emerald-700 rounded-2xl text-2xl">💵</span>
        </div>

        <!-- Ventas QR -->
        <div class="bg-white p-5 rounded-2xl border-2 border-blue-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-blue-800 uppercase tracking-wider">📱 Ganado por Pago QR</span>
                <h4 class="text-2xl font-extrabold text-blue-700 mt-1"><?= formatMoney($revenueByPayment['qr'] ?? 0) ?></h4>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5"><?= $countQr ?> ventas por QR</p>
            </div>
            <span class="p-3 bg-blue-50 text-blue-700 rounded-2xl text-2xl">📱</span>
        </div>

        <!-- Compras de Insumos -->
        <div class="bg-white p-5 rounded-2xl border border-cream-dark shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-coffee-medium uppercase">🛒 Compras de Insumos</span>
                <h4 class="text-xl font-extrabold text-coffee-dark mt-1"><?= formatMoney($totalCompras) ?></h4>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5"><?= count($compras) ?> registros</p>
            </div>
            <span class="p-3 bg-amber-50 text-amber-800 rounded-2xl text-xl">🛒</span>
        </div>

        <!-- Gastos Fijos -->
        <div class="bg-white p-5 rounded-2xl border border-cream-dark shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-coffee-medium uppercase">📌 Gastos Fijos</span>
                <h4 class="text-xl font-extrabold text-coffee-dark mt-1"><?= formatMoney($totalGastosFijos) ?></h4>
                <p class="text-[11px] text-slate-500 font-medium mt-0.5"><?= count($gastosFijos) ?> registros</p>
            </div>
            <span class="p-3 bg-amber-50 text-amber-800 rounded-2xl text-xl">📌</span>
        </div>
    </div>

    <?php if (!empty($revenueByPayment['sin_especificar']) && $revenueByPayment['sin_especificar'] > 0): ?>
        <div class="bg-amber-50 border-2 border-amber-300 p-4 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="text-2xl">⚠️</span>
                <div>
                    <h4 class="font-extrabold text-amber-900 text-sm">Ventas sin tipo de pago especificado</h4>
                    <p class="text-xs text-amber-800">Total en ventas sin especificar: <strong><?= formatMoney($revenueByPayment['sin_especificar']) ?></strong>. Asigna el tipo de pago a las ventas abajo.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>

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
                            <th class="p-4 text-center">Tipo Pago</th>
                            <th class="p-4 text-center">Items</th>
                            <th class="p-4 pr-6 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-cream-dark text-sm">
                        <?php if (empty($sales)): ?>
                            <tr>
                                <td colspan="5" class="p-6 text-center text-slate-400">No se registraron ventas en este período.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($sales as $sale): ?>
                                <?php 
                                $pm = strtolower($sale['payment_method'] ?? '');
                                $isQr = ($pm === 'qr');
                                $isEf = ($pm === 'efectivo');
                                $isUnspecified = empty($pm);
                                ?>
                                <tr class="hover:bg-cream/10 transition">
                                    <td class="p-4 pl-6 font-bold text-coffee-dark">#<?= $sale['id'] ?></td>
                                    <td class="p-4 text-slate-500 font-medium"><?= date('d/m/Y H:i', strtotime($sale['sale_date'])) ?></td>
                                    <td class="p-4 text-center">
                                        <?php if ($isUnspecified): ?>
                                            <div class="flex flex-col items-center gap-1">
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                                    ⚠️ Sin Especificar
                                                </span>
                                                <div class="flex gap-1 mt-0.5">
                                                    <button type="button" onclick="assignPaymentMethod(<?= $sale['id'] ?>, 'efectivo', this)" title="Asignar Efectivo"
                                                            class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded border border-emerald-200">
                                                        💵 Efectivo
                                                    </button>
                                                    <button type="button" onclick="assignPaymentMethod(<?= $sale['id'] ?>, 'qr', this)" title="Asignar QR"
                                                            class="bg-blue-50 hover:bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded border border-blue-200">
                                                        📱 QR
                                                    </button>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold <?= $isQr ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200' ?>">
                                                <?= $isQr ? '📱 QR' : '💵 Efectivo' ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
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
                    <span>📉</span> Detalle de Egresos (Compras y Gastos Fijos)
                </h3>
            </div>
            <div class="flex-grow overflow-y-auto max-h-[500px] custom-scrollbar p-4 space-y-6">
                <!-- Compras -->
                <div>
                    <h4 class="text-xs font-bold uppercase text-emerald-800 mb-2 flex items-center gap-1">
                        <span>🛒</span> Compras de Materia Prima
                    </h4>
                    <table class="w-full text-left text-xs border-collapse">
                        <tbody class="divide-y divide-cream-dark">
                            <?php if (empty($compras)): ?>
                                <tr><td class="py-2 text-slate-400">Sin compras registradas.</td></tr>
                            <?php else: ?>
                                <?php foreach ($compras as $c): ?>
                                    <tr>
                                        <td class="py-2 text-slate-500"><?= date('d/m/Y', strtotime($c['fecha'])) ?></td>
                                        <td class="py-2 font-bold text-coffee-dark"><?= e($c['material_name']) ?></td>
                                        <td class="py-2 text-right font-bold text-rose-600"><?= formatMoney($c['total']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Gastos Fijos -->
                <div>
                    <h4 class="text-xs font-bold uppercase text-rose-800 mb-2 flex items-center gap-1">
                        <span>📌</span> Gastos Fijos
                    </h4>
                    <table class="w-full text-left text-xs border-collapse">
                        <tbody class="divide-y divide-cream-dark">
                            <?php if (empty($gastosFijos)): ?>
                                <tr><td class="py-2 text-slate-400">Sin gastos fijos registrados.</td></tr>
                            <?php else: ?>
                                <?php foreach ($gastosFijos as $gf): ?>
                                    <tr>
                                        <td class="py-2 text-slate-500"><?= date('d/m/Y', strtotime($gf['fecha'])) ?></td>
                                        <td class="py-2 font-bold text-coffee-dark"><?= e($gf['nombre']) ?></td>
                                        <td class="py-2 text-right font-bold text-rose-600"><?= formatMoney($gf['precio']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ─── VENTAS POR CATEGORÍA ─────────────────────────────────────────── -->
    <?php if (!empty($revenueByCategory)): ?>
    <?php
    $maxCatRevenue = max(array_column($revenueByCategory, 'total_revenue')) ?: 1;
    $totalCatRevenue = array_sum(array_column($revenueByCategory, 'total_revenue'));

    // Inline SVG icons matching the categories view
    $catSvgIcons = [
        'hamburger'  => ['svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9h18"/><path d="M3 15h18"/><path d="M5 6a7 7 0 0 1 14 0"/><rect x="3" y="9" width="18" height="6" rx="1"/><path d="M5 18h14a2 2 0 0 0 2-2v-1H3v1a2 2 0 0 0 2 2z"/></svg>', 'bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'border' => 'border-amber-200'],
        'fries'       => ['svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 11v8a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-8"/><path d="M5 11l2-7h10l2 7"/><line x1="8" y1="4" x2="8" y2="11"/><line x1="12" y1="3" x2="12" y2="11"/><line x1="16" y1="4" x2="16" y2="11"/></svg>', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'border' => 'border-yellow-200'],
        'chicken'     => ['svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/><path d="M12 6S7 8 5 13c-1.5 4 1 8 5 8s7-2 8-6c1-3.5-1-7-6-9z"/><path d="M9 15c1 1.5 3 2 5 1"/></svg>', 'bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'border' => 'border-orange-200'],
        'drink_cup'   => ['svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 2h14l-1.5 16.5a2 2 0 0 1-2 1.5h-7a2 2 0 0 1-2-1.5L5 2z"/><line x1="9" y1="2" x2="9.5" y2="6"/><line x1="15" y1="2" x2="14.5" y2="6"/><path d="M3 2h18"/></svg>', 'bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'border' => 'border-blue-200'],
        'soda_bottle' => ['svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3h8v3l2 4v10a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V10l2-4V3z"/><line x1="8" y1="3" x2="8" y2="6"/><line x1="16" y1="3" x2="16" y2="6"/><line x1="6" y1="14" x2="18" y2="14"/></svg>', 'bg' => 'bg-cyan-100', 'text' => 'text-cyan-700', 'border' => 'border-cyan-200'],
        'package'     => ['svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="16.5" y1="9.4" x2="7.5" y2="4.21"/><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>', 'bg' => 'bg-stone-100', 'text' => 'text-stone-700', 'border' => 'border-stone-200'],
        'dessert'     => ['svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 13 8 13s8-7.75 8-13a8 8 0 0 0-8-8z"/><circle cx="12" cy="10" r="3"/></svg>', 'bg' => 'bg-pink-100', 'text' => 'text-pink-700', 'border' => 'border-pink-200'],
        'weekend'     => ['svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>', 'bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'border' => 'border-purple-200'],
        'coffee'      => ['svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V8z"/><line x1="6" y1="2" x2="6" y2="4"/><line x1="10" y1="2" x2="10" y2="4"/><line x1="14" y1="2" x2="14" y2="4"/></svg>', 'bg' => 'bg-stone-100', 'text' => 'text-stone-700', 'border' => 'border-stone-200'],
        'pizza'       => ['svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2C6.477 2 2 6.477 2 12l10 10L22 12C22 6.477 17.523 2 12 2z"/><path d="M12 2v10"/><circle cx="9" cy="9" r="1" fill="currentColor"/><circle cx="14" cy="13" r="1" fill="currentColor"/></svg>', 'bg' => 'bg-red-100', 'text' => 'text-red-700', 'border' => 'border-red-200'],
        'salad'       => ['svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 14s3-3 6-3 6 3 9 3 6-3 6-3"/><path d="M2 20h20"/><path d="M8 14V8a4 4 0 0 1 8 0v6"/></svg>', 'bg' => 'bg-green-100', 'text' => 'text-green-700', 'border' => 'border-green-200'],
        'combo'       => ['svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8"/><path d="M12 17v4"/></svg>', 'bg' => 'bg-indigo-100', 'text' => 'text-indigo-700', 'border' => 'border-indigo-200'],
    ];
    // fallback for unknown icons
    $defaultIcon = ['svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><circle cx="12" cy="16" r="0.5" fill="currentColor"/></svg>', 'bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'border' => 'border-slate-200'];
    ?>
    <div class="bg-white rounded-2xl border border-cream-dark shadow-sm overflow-hidden">
        <!-- Section Header -->
        <div class="p-5 border-b border-cream-dark bg-gradient-to-r from-coffee-dark/5 to-transparent flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-accent/15 text-accent flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                </div>
                <div>
                    <h3 class="font-heading font-extrabold text-base text-coffee-dark">Ventas por Categoría</h3>
                    <p class="text-xs text-coffee-light font-medium"><?= count($revenueByCategory) ?> categorías con ventas en el período</p>
                </div>
            </div>
            <div class="text-right">
                <span class="text-xs font-bold text-coffee-medium uppercase">Total categorías</span>
                <div class="text-lg font-extrabold text-coffee-dark"><?= formatMoney($totalCatRevenue) ?></div>
            </div>
        </div>

        <!-- Category Cards Grid -->
        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($revenueByCategory as $idx => $catRow):
                $iconKey  = $catRow['category_icon'] ?? '';
                $iconData = $catSvgIcons[$iconKey] ?? $defaultIcon;
                $pct      = $maxCatRevenue > 0 ? round(($catRow['total_revenue'] / $maxCatRevenue) * 100) : 0;
                $sharePct = $totalCatRevenue > 0 ? round(($catRow['total_revenue'] / $totalCatRevenue) * 100, 1) : 0;

                // Rank badge colors
                $rankColors = ['bg-amber-400 text-amber-900', 'bg-slate-300 text-slate-700', 'bg-amber-600/60 text-amber-900'];
                $rankColor  = $rankColors[$idx] ?? 'bg-cream text-coffee-medium';
            ?>
            <div class="bg-white border-2 <?= $iconData['border'] ?> rounded-2xl p-4 flex flex-col gap-3 hover:shadow-md transition-shadow duration-200">
                <!-- Card Header -->
                <div class="flex items-center gap-3">
                    <!-- Rank badge -->
                    <?php if ($idx < 3): ?>
                    <div class="w-5 h-5 rounded-full <?= $rankColor ?> text-[10px] font-extrabold flex items-center justify-center flex-shrink-0"><?= $idx + 1 ?></div>
                    <?php endif; ?>
                    <!-- Category Icon -->
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 <?= $iconData['bg'] ?> <?= $iconData['text'] ?> [&_svg]:w-5 [&_svg]:h-5 border <?= $iconData['border'] ?>">
                        <?= $iconData['svg'] ?>
                    </div>
                    <!-- Name -->
                    <div class="min-w-0">
                        <h4 class="text-sm font-extrabold text-coffee-dark leading-tight truncate"><?= e($catRow['category_name']) ?></h4>
                        <span class="text-[10px] text-slate-400 font-medium"><?= number_format((int)$catRow['items_sold']) ?> items vendidos</span>
                    </div>
                </div>

                <!-- Revenue + Share -->
                <div class="flex items-end justify-between">
                    <div>
                        <span class="text-xs text-coffee-light font-medium block">Ganancia total</span>
                        <span class="text-xl font-extrabold text-emerald-700"><?= formatMoney($catRow['total_revenue']) ?></span>
                    </div>
                    <span class="text-xs font-bold <?= $iconData['text'] ?> <?= $iconData['bg'] ?> px-2.5 py-1 rounded-xl border <?= $iconData['border'] ?>"><?= $sharePct ?>%</span>
                </div>

                <!-- Progress bar -->
                <div class="w-full bg-slate-100 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all duration-700 <?= str_replace('text-', 'bg-', explode(' ', $iconData['text'])[0]) ?>"
                         style="width: <?= $pct ?>%; min-width: 4px;"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- ══════════════════════════════════════════════════════════
         SECCIÓN: ANÁLISIS DE PRECIOS HISTÓRICOS POR PRODUCTO
    ═══════════════════════════════════════════════════════════ -->
    <?php if (!empty($priceRangeReport)): ?>
    <div class="bg-white rounded-2xl border border-cream-dark shadow-sm overflow-hidden">
        <div class="p-5 border-b border-cream-dark flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-xl border border-amber-200">💰</div>
            <div>
                <h2 class="font-heading font-extrabold text-coffee-dark text-lg">Análisis de Precios Históricos por Producto</h2>
                <p class="text-xs text-coffee-light mt-0.5">Productos que se vendieron con diferentes precios en el período seleccionado</p>
            </div>
        </div>

        <div class="p-5 space-y-5">
            <?php foreach ($priceRangeReport as $prodData): ?>
            <div class="border border-cream-dark rounded-xl overflow-hidden">
                <!-- Product header -->
                <div class="bg-cream/60 px-4 py-3 flex items-center gap-3 border-b border-cream-dark">
                    <span class="text-lg"><?= e($prodData['product_icon']) ?></span>
                    <span class="font-heading font-extrabold text-coffee-dark text-sm"><?= e($prodData['product_name']) ?></span>
                    <span class="ml-auto text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full">
                        <?= count($prodData['prices']) ?> precios distintos
                    </span>
                </div>
                <!-- Price rows table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-xs font-extrabold text-coffee-medium uppercase tracking-wide border-b border-cream-dark">
                            <tr>
                                <th class="px-4 py-3 text-left">Precio Unitario</th>
                                <th class="px-4 py-3 text-left">Vigente Desde</th>
                                <th class="px-4 py-3 text-left">Vigente Hasta</th>
                                <th class="px-4 py-3 text-center">Unidades Vendidas</th>
                                <th class="px-4 py-3 text-right">Subtotal Recaudado</th>
                                <th class="px-4 py-3 text-left">Comparativa</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-cream-dark">
                            <?php 
                            $totalProdQty = array_sum(array_column($prodData['prices'], 'qty'));
                            foreach ($prodData['prices'] as $pr): 
                                $pctQty = $totalProdQty > 0 ? round(($pr['qty'] / $totalProdQty) * 100) : 0;
                                $vDesde = !empty($pr['vigencia_desde']) ? $pr['vigencia_desde'] : $pr['date_from'];
                                $vHasta = !empty($pr['vigencia_hasta']) ? $pr['vigencia_hasta'] : $pr['date_to'];
                            ?>
                            <tr class="hover:bg-cream/30 transition-colors">
                                <td class="px-4 py-3">
                                    <span class="text-sm font-extrabold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-lg">
                                        <?= formatMoney($pr['price']) ?> c/u
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-700 font-bold"><?= date('d/m/Y', strtotime($vDesde)) ?></td>
                                <td class="px-4 py-3 text-xs text-slate-700 font-bold"><?= date('d/m/Y', strtotime($vHasta)) ?></td>
                                <td class="px-4 py-3 text-center font-extrabold text-coffee-dark">
                                    <span class="bg-coffee-dark/10 text-coffee-dark px-2.5 py-0.5 rounded-full text-xs">
                                        <?= number_format($pr['qty']) ?> unidad<?= $pr['qty'] != 1 ? 'es' : '' ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right font-extrabold text-coffee-dark text-sm"><?= formatMoney($pr['total']) ?></td>
                                <td class="px-4 py-3 w-40">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-slate-100 rounded-full h-2 overflow-hidden">
                                            <div class="bg-amber-500 h-2 rounded-full" style="width: <?= $pctQty ?>%"></div>
                                        </div>
                                        <span class="text-[10px] font-bold text-slate-500"><?= $pctQty ?>%</span>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-coffee-dark/5 border-t border-cream-dark">
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-xs font-extrabold text-coffee-dark uppercase">Total Vendido del Producto</td>
                                <td class="px-4 py-3 text-center font-extrabold text-coffee-dark text-sm">
                                    <?= number_format($totalProdQty) ?> unidades
                                </td>
                                <td class="px-4 py-3 text-right font-extrabold text-emerald-700 text-sm">
                                    <?= formatMoney(array_sum(array_column($prodData['prices'], 'total'))) ?>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function assignPaymentMethod(saleId, method, btnElement) {
    fetch(`${window.BASE_URL}/sales/update-payment-method`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `sale_id=${saleId}&payment_method=${method}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const container = btnElement.closest('td');
            if (method === 'efectivo') {
                container.innerHTML = `<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">💵 Efectivo</span>`;
            } else {
                container.innerHTML = `<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">📱 QR</span>`;
            }
        } else {
            alert(data.message || 'Error al asignar método de pago');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Error de conexión al asignar el método de pago');
    });
}
</script>
