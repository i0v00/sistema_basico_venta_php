<div class="space-y-8 animate-slide-up">
    <!-- Header -->
    <div class="bg-white p-6 rounded-2xl border border-cream-dark shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-heading font-extrabold text-coffee-dark">💸 Compras y Gastos Fijos</h1>
            <p class="text-coffee-light mt-1">Registra compras de materias primas (aumentan stock) y gastos fijos del restaurante.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= BASE_URL ?>/reports" class="inline-flex items-center gap-2 bg-coffee-dark hover:bg-coffee-medium text-white px-5 py-2.5 rounded-xl font-bold shadow-md transition duration-200 active:scale-95 text-sm">
                📈 Ver Reportes Financieros
            </a>
        </div>
    </div>

    <!-- Alert Banners -->
    <?php if ($flash = getFlash('success')): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-semibold">
            <?= e($flash) ?>
        </div>
    <?php endif; ?>
    <?php if ($flash = getFlash('error')): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm font-semibold">
            <?= e($flash) ?>
        </div>
    <?php endif; ?>

    <!-- 2 Registration Forms Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- 1. COMPRA DE MATERIA PRIMA -->
        <div class="bg-white p-6 rounded-2xl border border-cream-dark shadow-sm space-y-4">
            <h2 class="font-heading font-extrabold text-lg text-coffee-dark flex items-center gap-2 border-b border-cream-dark pb-3">
                <span>🛒</span> Compra de Materia Prima
            </h2>
            <p class="text-xs text-coffee-light">Al guardar, se sumará automáticamente la cantidad al stock de la materia prima.</p>
            <form method="POST" action="<?= BASE_URL ?>/admin-expenses/compra/save" class="space-y-4">
                <div>
                    <label for="raw_material_id" class="block text-xs font-bold text-coffee-medium mb-1 uppercase">Insumo / Materia Prima</label>
                    <select name="raw_material_id" id="raw_material_id" required onchange="autoFillMaterialPrice()"
                            class="w-full px-4 py-2.5 rounded-xl border border-cream-dark focus:outline-none focus:ring-2 focus:ring-accent text-sm">
                        <option value="">-- Seleccionar Insumo --</option>
                        <?php foreach ($rawMaterials as $mat): ?>
                            <option value="<?= $mat['id'] ?>" data-price="<?= $mat['price'] ?>" data-unit="<?= e($mat['unit']) ?>">
                                <?= e($mat['name']) ?> (Ref: <?= formatMoney($mat['price']) ?> / <?= e($mat['unit']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="compra_cantidad" class="block text-xs font-bold text-coffee-medium mb-1 uppercase">Cantidad</label>
                        <input type="number" name="cantidad" id="compra_cantidad" step="0.01" min="0.01" value="1" required oninput="calcCompraTotal()"
                               class="w-full px-4 py-2.5 rounded-xl border border-cream-dark focus:outline-none focus:ring-2 focus:ring-accent text-sm">
                    </div>
                    <div>
                        <label for="compra_precio" class="block text-xs font-bold text-coffee-medium mb-1 uppercase">P. Unit (Bs)</label>
                        <input type="number" name="precio_unitario" id="compra_precio" step="0.01" min="0" value="0.00" required oninput="calcCompraTotal()"
                               class="w-full px-4 py-2.5 rounded-xl border border-cream-dark focus:outline-none focus:ring-2 focus:ring-accent text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-coffee-medium mb-1 uppercase">Total Compra</label>
                        <div class="w-full px-3 py-2.5 rounded-xl bg-cream border border-cream-dark font-extrabold text-coffee-dark text-sm" id="compra_total_display">
                            Bs. 0.00
                        </div>
                    </div>
                    <div>
                        <label for="compra_fecha" class="block text-xs font-bold text-coffee-medium mb-1 uppercase">Fecha</label>
                        <input type="date" name="fecha" id="compra_fecha" value="<?= date('Y-m-d') ?>" required
                               class="w-full px-3 py-2.5 rounded-xl border border-cream-dark focus:outline-none focus:ring-2 focus:ring-accent text-sm">
                    </div>
                </div>

                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold px-4 py-2.5 rounded-xl text-sm transition duration-200 shadow-sm">
                    🛒 Registrar Compra (+Stock)
                </button>
            </form>
        </div>

        <!-- 2. GASTOS FIJOS -->
        <div class="bg-white p-6 rounded-2xl border border-cream-dark shadow-sm space-y-4">
            <h2 class="font-heading font-extrabold text-lg text-coffee-dark flex items-center gap-2 border-b border-cream-dark pb-3">
                <span>📌</span> Gasto Fijo
            </h2>
            <p class="text-xs text-coffee-light">Gastos periódicos recurrentes (ej: alquiler, luz, agua, internet).</p>
            <form method="POST" action="<?= BASE_URL ?>/admin-expenses/gasto-fijo/save" class="space-y-4">
                <div>
                    <label for="fijo_nombre" class="block text-xs font-bold text-coffee-medium mb-1 uppercase">Nombre del Gasto Fijo</label>
                    <input type="text" name="nombre" id="fijo_nombre" required placeholder="Ej: Alquiler Local, Servicio Luz"
                           class="w-full px-4 py-2.5 rounded-xl border border-cream-dark focus:outline-none focus:ring-2 focus:ring-accent text-sm">
                </div>

                <div>
                    <label for="fijo_precio" class="block text-xs font-bold text-coffee-medium mb-1 uppercase">Precio / Monto (Bs)</label>
                    <input type="number" name="precio" id="fijo_precio" step="0.01" min="0" value="0.00" required
                           class="w-full px-4 py-2.5 rounded-xl border border-cream-dark focus:outline-none focus:ring-2 focus:ring-accent text-sm">
                </div>

                <div>
                    <label for="fijo_fecha" class="block text-xs font-bold text-coffee-medium mb-1 uppercase">Fecha del Gasto</label>
                    <input type="date" name="fecha" id="fijo_fecha" value="<?= date('Y-m-d') ?>" required
                           class="w-full px-4 py-2.5 rounded-xl border border-cream-dark focus:outline-none focus:ring-2 focus:ring-accent text-sm">
                </div>

                <button type="submit" class="w-full bg-coffee-dark hover:bg-coffee-medium text-white font-extrabold px-4 py-2.5 rounded-xl text-sm transition duration-200 shadow-sm">
                    📌 Guardar Gasto Fijo
                </button>
            </form>
        </div>
    </div>

    <!-- Filter Bar for Tables -->
    <div class="bg-white p-4 rounded-2xl border border-cream-dark shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <h3 class="font-heading font-extrabold text-base text-coffee-dark">📅 Historial de Registros</h3>
        <form method="GET" action="<?= BASE_URL ?>/admin-expenses" class="flex items-center gap-2">
            <input type="date" name="start_date" value="<?= e($startDate) ?>" class="px-3 py-1.5 rounded-lg border border-cream-dark text-xs focus:outline-none focus:ring-2 focus:ring-accent">
            <span class="text-xs text-coffee-medium font-bold">a</span>
            <input type="date" name="end_date" value="<?= e($endDate) ?>" class="px-3 py-1.5 rounded-lg border border-cream-dark text-xs focus:outline-none focus:ring-2 focus:ring-accent">
            <button type="submit" class="bg-coffee-dark hover:bg-coffee-medium text-white px-3.5 py-1.5 rounded-lg text-xs font-bold transition">
                Filtrar Registros
            </button>
        </form>
    </div>

    <!-- 2 Tables Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Table 1: Compras -->
        <div class="bg-white rounded-2xl border border-cream-dark shadow-sm overflow-hidden flex flex-col">
            <div class="p-4 border-b border-cream-dark bg-emerald-50/50 flex justify-between items-center">
                <h3 class="font-heading font-extrabold text-sm text-emerald-950 flex items-center gap-2">
                    <span>🛒</span> Compras de Materia Prima
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-cream text-coffee-medium border-b border-cream-dark text-[11px] uppercase font-bold tracking-wider">
                            <th class="p-3 pl-4">Fecha</th>
                            <th class="p-3">Insumo</th>
                            <th class="p-3 text-center">Cant.</th>
                            <th class="p-3 text-right">Total</th>
                            <th class="p-3 pr-4 text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-cream-dark text-xs">
                        <?php if (empty($compras)): ?>
                            <tr><td colspan="5" class="p-4 text-center text-slate-400">Sin compras registradas.</td></tr>
                        <?php else: ?>
                            <?php foreach ($compras as $c): ?>
                                <tr class="hover:bg-cream/10 transition">
                                    <td class="p-3 pl-4 text-slate-500 font-medium"><?= date('d/m/Y', strtotime($c['fecha'])) ?></td>
                                    <td class="p-3 font-bold text-coffee-dark"><?= e($c['material_name']) ?></td>
                                    <td class="p-3 text-center text-coffee-medium font-semibold"><?= number_format($c['cantidad'], 2) ?> <?= e($c['unit']) ?></td>
                                    <td class="p-3 text-right font-extrabold text-emerald-700"><?= formatMoney($c['total']) ?></td>
                                    <td class="p-3 pr-4 text-center">
                                        <form method="POST" action="<?= BASE_URL ?>/admin-expenses/compra/delete" onsubmit="return confirm('¿Eliminar esta compra? Se descontará del stock.');">
                                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                            <button type="submit" class="text-rose-600 hover:text-rose-800 text-[11px] font-bold bg-rose-50 px-2 py-1 rounded border border-rose-200">🗑️</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Table 2: Gastos Fijos -->
        <div class="bg-white rounded-2xl border border-cream-dark shadow-sm overflow-hidden flex flex-col">
            <div class="p-4 border-b border-cream-dark bg-rose-50/50 flex justify-between items-center">
                <h3 class="font-heading font-extrabold text-sm text-rose-950 flex items-center gap-2">
                    <span>📌</span> Gastos Fijos
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-cream text-coffee-medium border-b border-cream-dark text-[11px] uppercase font-bold tracking-wider">
                            <th class="p-3 pl-4">Fecha</th>
                            <th class="p-3">Nombre</th>
                            <th class="p-3 text-right">Monto</th>
                            <th class="p-3 pr-4 text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-cream-dark text-xs">
                        <?php if (empty($gastosFijos)): ?>
                            <tr><td colspan="4" class="p-4 text-center text-slate-400">Sin gastos fijos registrados.</td></tr>
                        <?php else: ?>
                            <?php foreach ($gastosFijos as $gf): ?>
                                <tr class="hover:bg-cream/10 transition">
                                    <td class="p-3 pl-4 text-slate-500 font-medium"><?= date('d/m/Y', strtotime($gf['fecha'])) ?></td>
                                    <td class="p-3 font-bold text-coffee-dark"><?= e($gf['nombre']) ?></td>
                                    <td class="p-3 text-right font-extrabold text-rose-600"><?= formatMoney($gf['precio']) ?></td>
                                    <td class="p-3 pr-4 text-center">
                                        <form method="POST" action="<?= BASE_URL ?>/admin-expenses/gasto-fijo/delete" onsubmit="return confirm('¿Eliminar gasto fijo?');">
                                            <input type="hidden" name="id" value="<?= $gf['id'] ?>">
                                            <button type="submit" class="text-rose-600 hover:text-rose-800 text-[11px] font-bold bg-rose-50 px-2 py-1 rounded border border-rose-200">🗑️</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function autoFillMaterialPrice() {
        const sel = document.getElementById('raw_material_id');
        const opt = sel.options[sel.selectedIndex];
        if (opt && opt.value) {
            const price = parseFloat(opt.getAttribute('data-price') || 0);
            document.getElementById('compra_precio').value = price.toFixed(2);
        } else {
            document.getElementById('compra_precio').value = "0.00";
        }
        calcCompraTotal();
    }

    function calcCompraTotal() {
        const qty = parseFloat(document.getElementById('compra_cantidad').value || 0);
        const price = parseFloat(document.getElementById('compra_precio').value || 0);
        const total = qty * price;
        document.getElementById('compra_total_display').textContent = 'Bs. ' + total.toFixed(2);
    }
</script>
