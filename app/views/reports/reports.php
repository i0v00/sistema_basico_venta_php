<div class="space-y-8 animate-slide-up">
    <!-- Header -->
    <div class="bg-white p-6 rounded-2xl border border-cream-dark shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-heading font-extrabold text-coffee-dark">📊 Informe General de Balance</h1>
            <p class="text-coffee-light mt-1 font-medium">
                Período: <span class="font-extrabold text-coffee-dark bg-cream border border-cream-dark px-2.5 py-0.5 rounded-lg">Del <?= date('d/m/Y', strtotime($startDate)) ?> al <?= date('d/m/Y', strtotime($endDate)) ?></span>
            </p>
        </div>
        <div>
            <a href="<?= BASE_URL ?>/reports/print?filter_mode=<?= e($filterMode) ?>&date=<?= isset($_GET['date']) ? e($_GET['date']) : date('Y-m-d') ?>&month=<?= e($selectedMonth ?? date('m')) ?>&year=<?= e($selectedYear ?? date('Y')) ?>&start_date=<?= e($startDate) ?>&end_date=<?= e($endDate) ?>" target="_blank"
               class="inline-flex items-center gap-2 bg-coffee-dark hover:bg-coffee-medium text-white px-5 py-3 rounded-xl font-extrabold shadow-md transition duration-200 active:scale-95 text-sm">
                🖨️ Imprimir PDF / A4
            </a>
        </div>
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
                                                <form method="POST" action="<?= BASE_URL ?>/sales/update-payment-method" class="flex gap-1 mt-0.5">
                                                    <input type="hidden" name="sale_id" value="<?= $sale['id'] ?>">
                                                    <input type="hidden" name="redirect_url" value="<?= e($_SERVER['REQUEST_URI']) ?>">
                                                    <button type="submit" name="payment_method" value="efectivo" title="Asignar Efectivo"
                                                            class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded border border-emerald-200">
                                                        💵 Efectivo
                                                    </button>
                                                    <button type="submit" name="payment_method" value="qr" title="Asignar QR"
                                                            class="bg-blue-50 hover:bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-0.5 rounded border border-blue-200">
                                                        📱 QR
                                                    </button>
                                                </form>
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
</div>
