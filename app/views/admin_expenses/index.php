<div class="space-y-8 animate-slide-up">
    <!-- Header -->
    <div class="bg-white p-6 rounded-2xl border border-cream-dark shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-heading font-extrabold text-coffee-dark">💸 Gastos Administrativos</h1>
            <p class="text-coffee-light mt-1">Administra los productos administrativos e insumos no comerciales y registra los gastos diarios.</p>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Column 1: Manage Products & Register Expense -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Part A: Register Expense Form -->
            <div class="bg-white p-6 rounded-2xl border border-cream-dark shadow-sm space-y-4">
                <h2 class="font-heading font-extrabold text-lg text-coffee-dark flex items-center gap-2">
                    <span>📝</span> Registrar Gasto
                </h2>
                <form method="POST" action="<?= BASE_URL ?>/admin-expenses/save" class="space-y-4">
                    <input type="hidden" name="id" id="expense_id" value="">
                    
                    <div>
                        <label for="producto_id" class="block text-xs font-bold text-coffee-medium mb-1 uppercase">Concepto / Producto</label>
                        <select name="producto_id" id="expense_producto_id" required onchange="updateUnitPriceExpense()"
                                class="w-full px-4 py-2.5 rounded-xl border border-cream-dark focus:outline-none focus:ring-2 focus:ring-accent text-sm">
                            <option value="">-- Seleccionar --</option>
                            <?php foreach ($activeProducts as $prod): ?>
                                <option value="<?= $prod['id'] ?>" data-price="<?= $prod['precio_unitario'] ?>">
                                    <?= e($prod['nombre']) ?> (<?= formatMoney($prod['precio_unitario']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="cantidad" class="block text-xs font-bold text-coffee-medium mb-1 uppercase">Cantidad</label>
                            <input type="number" name="cantidad" id="expense_cantidad" min="1" value="1" required oninput="calculateExpenseTotal()"
                                   class="w-full px-4 py-2.5 rounded-xl border border-cream-dark focus:outline-none focus:ring-2 focus:ring-accent text-sm">
                        </div>
                        <div>
                            <label for="precio_unitario" class="block text-xs font-bold text-coffee-medium mb-1 uppercase">P. Unitario (Bs)</label>
                            <input type="number" name="precio_unitario" id="expense_precio_unitario" step="0.01" min="0" value="0.00" readonly required oninput="calculateExpenseTotal()"
                                   class="w-full px-4 py-2.5 rounded-xl border border-cream-dark bg-cream/50 focus:outline-none text-sm cursor-not-allowed select-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-coffee-medium mb-1 uppercase">Total Gasto</label>
                            <div class="w-full px-4 py-2.5 rounded-xl bg-cream border border-cream-dark font-extrabold text-coffee-dark text-sm" id="expense_total_display">
                                Bs. 0.00
                            </div>
                        </div>
                        <div>
                            <label for="fecha" class="block text-xs font-bold text-coffee-medium mb-1 uppercase">Fecha</label>
                            <input type="date" name="fecha" id="expense_fecha" value="<?= date('Y-m-d') ?>" required
                                   class="w-full px-4 py-2.5 rounded-xl border border-cream-dark focus:outline-none focus:ring-2 focus:ring-accent text-sm">
                        </div>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button type="submit" class="flex-1 bg-accent hover:bg-accent-dark text-white font-extrabold px-4 py-2.5 rounded-xl text-sm transition duration-200 shadow-sm">
                            💾 Guardar Gasto
                        </button>
                        <button type="button" onclick="resetExpenseForm()" class="bg-cream-dark hover:bg-cream-dark/80 text-coffee-dark font-bold px-4 py-2.5 rounded-xl text-xs transition">
                            Limpiar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Part B: Manage Administrative Products Form -->
            <div class="bg-white p-6 rounded-2xl border border-cream-dark shadow-sm space-y-4">
                <h2 class="font-heading font-extrabold text-lg text-coffee-dark flex items-center gap-2">
                    <span>📦</span> Productos Administrativos
                </h2>
                <form method="POST" action="<?= BASE_URL ?>/admin-expenses/product/save" class="space-y-4">
                    <input type="hidden" name="id" id="prod_id" value="">
                    
                    <div>
                        <label for="prod_nombre" class="block text-xs font-bold text-coffee-medium mb-1 uppercase">Nombre del Producto</label>
                        <input type="text" name="nombre" id="prod_nombre" required placeholder="Ej: Platos de cartón, Servilletas"
                               class="w-full px-4 py-2.5 rounded-xl border border-cream-dark focus:outline-none focus:ring-2 focus:ring-accent text-sm">
                    </div>

                    <div>
                        <label for="prod_precio" class="block text-xs font-bold text-coffee-medium mb-1 uppercase">Precio Unitario Estimado (Bs)</label>
                        <input type="number" name="precio_unitario" id="prod_precio" step="0.01" min="0" value="0.00" required
                               class="w-full px-4 py-2.5 rounded-xl border border-cream-dark focus:outline-none focus:ring-2 focus:ring-accent text-sm">
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="activo" id="prod_activo" value="1" checked
                               class="rounded border-cream-dark text-accent focus:ring-accent w-4.5 h-4.5">
                        <label for="prod_activo" class="text-sm font-semibold text-coffee-dark">Producto Activo</label>
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button type="submit" class="flex-1 bg-coffee-dark hover:bg-coffee-medium text-white font-extrabold px-4 py-2.5 rounded-xl text-sm transition duration-200 shadow-sm">
                            💾 Guardar Producto
                        </button>
                        <button type="button" onclick="resetProductForm()" class="bg-cream-dark hover:bg-cream-dark/80 text-coffee-dark font-bold px-4 py-2.5 rounded-xl text-xs transition">
                            Limpiar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Column 2: Lists & Logs -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Administrative Products Table -->
            <div class="bg-white rounded-2xl border border-cream-dark shadow-sm overflow-hidden">
                <div class="p-6 border-b border-cream-dark bg-coffee-dark/5">
                    <h3 class="font-heading font-extrabold text-lg text-coffee-dark">📋 Catálogo de Productos Administrativos</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-cream text-coffee-medium border-b border-cream-dark text-xs uppercase font-bold tracking-wider">
                                <th class="p-4 pl-6">ID</th>
                                <th class="p-4">Nombre</th>
                                <th class="p-4 text-right">P. Unitario</th>
                                <th class="p-4 text-center">Estado</th>
                                <th class="p-4 pr-6 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-cream-dark text-sm">
                            <?php if (empty($products)): ?>
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-slate-400">No hay productos administrativos registrados.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($products as $prod): ?>
                                    <tr class="hover:bg-cream/10 transition">
                                        <td class="p-4 pl-6 text-slate-500 font-semibold">#<?= $prod['id'] ?></td>
                                        <td class="p-4 font-bold text-coffee-dark"><?= e($prod['nombre']) ?></td>
                                        <td class="p-4 text-right font-bold text-coffee-medium"><?= formatMoney($prod['precio_unitario']) ?></td>
                                        <td class="p-4 text-center">
                                            <?php if ((int)$prod['activo'] === 1): ?>
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">ACTIVO</span>
                                            <?php else: ?>
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-red-100 text-red-800 border border-red-200">INACTIVO (ELIMINADO)</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="p-4 pr-6 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <button onclick="editProduct(<?= htmlspecialchars(json_encode($prod)) ?>)"
                                                        class="bg-coffee-medium/10 hover:bg-coffee-medium/20 text-coffee-dark text-xs font-bold px-3 py-1.5 rounded-lg transition">
                                                    ✏️ Editar
                                                </button>
                                                <?php if ((int)$prod['activo'] === 1): ?>
                                                    <form method="POST" action="<?= BASE_URL ?>/admin-expenses/product/delete" class="inline" onsubmit="return confirm('¿Eliminar producto de forma lógica?');">
                                                        <input type="hidden" name="id" value="<?= $prod['id'] ?>">
                                                        <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold px-3 py-1.5 rounded-lg transition">
                                                            🗑️ Borrar
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Expense Log Table with Date Filters -->
            <div class="bg-white rounded-2xl border border-cream-dark shadow-sm overflow-hidden">
                <div class="p-6 border-b border-cream-dark bg-coffee-dark/5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <h3 class="font-heading font-extrabold text-lg text-coffee-dark">💸 Historial de Gastos Registrados</h3>
                    <form method="GET" action="<?= BASE_URL ?>/admin-expenses" class="flex items-center gap-2">
                        <input type="date" name="start_date" value="<?= e($startDate) ?>" class="px-3 py-1.5 rounded-lg border border-cream-dark text-xs focus:outline-none focus:ring-2 focus:ring-accent">
                        <span class="text-xs text-coffee-medium font-bold">a</span>
                        <input type="date" name="end_date" value="<?= e($endDate) ?>" class="px-3 py-1.5 rounded-lg border border-cream-dark text-xs focus:outline-none focus:ring-2 focus:ring-accent">
                        <button type="submit" class="bg-coffee-dark hover:bg-coffee-medium text-white px-3 py-1.5 rounded-lg text-xs font-bold transition">
                            Filtrar
                        </button>
                    </form>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-cream text-coffee-medium border-b border-cream-dark text-xs uppercase font-bold tracking-wider">
                                <th class="p-4 pl-6">Fecha</th>
                                <th class="p-4">Producto</th>
                                <th class="p-4 text-center">Cant.</th>
                                <th class="p-4 text-right">P. Unitario</th>
                                <th class="p-4 text-right">Total</th>
                                <th class="p-4 pr-6 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-cream-dark text-sm">
                            <?php if (empty($expenses)): ?>
                                <tr>
                                    <td colspan="6" class="p-6 text-center text-slate-400">No hay gastos registrados en el período seleccionado.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($expenses as $exp): ?>
                                    <tr class="hover:bg-cream/10 transition">
                                        <td class="p-4 pl-6 text-slate-500 font-medium"><?= date('d/m/Y', strtotime($exp['fecha'])) ?></td>
                                        <td class="p-4 font-bold text-coffee-dark">
                                            <?= e($exp['producto_nombre']) ?>
                                        </td>
                                        <td class="p-4 text-center font-bold text-coffee-medium"><?= $exp['cantidad'] ?></td>
                                        <td class="p-4 text-right font-semibold text-slate-500"><?= formatMoney($exp['precio_unitario']) ?></td>
                                        <td class="p-4 text-right font-extrabold text-rose-600"><?= formatMoney($exp['total']) ?></td>
                                        <td class="p-4 pr-6 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <button onclick="editExpense(<?= htmlspecialchars(json_encode($exp)) ?>)"
                                                        class="bg-coffee-medium/10 hover:bg-coffee-medium/20 text-coffee-dark text-xs font-bold px-3 py-1.5 rounded-lg transition">
                                                    ✏️ Editar
                                                </button>
                                                <form method="POST" action="<?= BASE_URL ?>/admin-expenses/delete" class="inline" onsubmit="return confirm('¿Eliminar este registro de gasto de forma permanente?');">
                                                    <input type="hidden" name="id" value="<?= $exp['id'] ?>">
                                                    <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold px-3 py-1.5 rounded-lg transition">
                                                        🗑️ Borrar
                                                    </button>
                                                </form>
                                            </div>
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
</div>

<script>
    // Expense Scripting
    function updateUnitPriceExpense() {
        const select = document.getElementById('expense_producto_id');
        const selectedOption = select.options[select.selectedIndex];
        if (selectedOption && selectedOption.value) {
            const price = parseFloat(selectedOption.getAttribute('data-price') || 0);
            document.getElementById('expense_precio_unitario').value = price.toFixed(2);
        } else {
            document.getElementById('expense_precio_unitario').value = "0.00";
        }
        calculateExpenseTotal();
    }

    function calculateExpenseTotal() {
        const qty = parseInt(document.getElementById('expense_cantidad').value || 0);
        const price = parseFloat(document.getElementById('expense_precio_unitario').value || 0);
        const total = qty * price;
        document.getElementById('expense_total_display').textContent = 'Bs. ' + total.toFixed(2);
    }

    function editExpense(exp) {
        document.getElementById('expense_id').value = exp.id;
        document.getElementById('expense_producto_id').value = exp.producto_id;
        document.getElementById('expense_cantidad').value = exp.cantidad;
        document.getElementById('expense_precio_unitario').value = parseFloat(exp.precio_unitario).toFixed(2);
        document.getElementById('expense_fecha').value = exp.fecha;
        calculateExpenseTotal();
        document.getElementById('expense_producto_id').scrollIntoView({ behavior: 'smooth' });
    }

    function resetExpenseForm() {
        document.getElementById('expense_id').value = '';
        document.getElementById('expense_producto_id').value = '';
        document.getElementById('expense_cantidad').value = '1';
        document.getElementById('expense_precio_unitario').value = '0.00';
        document.getElementById('expense_fecha').value = '<?= date('Y-m-d') ?>';
        calculateExpenseTotal();
    }

    // Product Scripting
    function editProduct(prod) {
        document.getElementById('prod_id').value = prod.id;
        document.getElementById('prod_nombre').value = prod.nombre;
        document.getElementById('prod_precio').value = parseFloat(prod.precio_unitario).toFixed(2);
        document.getElementById('prod_activo').checked = (parseInt(prod.activo) === 1);
        document.getElementById('prod_nombre').scrollIntoView({ behavior: 'smooth' });
    }

    function resetProductForm() {
        document.getElementById('prod_id').value = '';
        document.getElementById('prod_nombre').value = '';
        document.getElementById('prod_precio').value = '0.00';
        document.getElementById('prod_activo').checked = true;
    }
</script>
