<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-heading font-extrabold text-coffee-dark">Inventario de Materia Prima</h1>
            <p class="text-coffee-light">Gestiona los insumos e ingredientes base del restaurante</p>
        </div>
        <div>
            <a href="<?= BASE_URL ?>/raw-materials/create" class="inline-flex items-center gap-2 bg-accent hover:bg-accent-dark text-white px-5 py-3 rounded-xl font-bold shadow-md transition duration-200">
                <span>➕</span> Nuevo Insumo
            </a>
        </div>
    </div>

    <!-- Filter/Search Bar -->
    <div class="bg-white p-4 rounded-2xl border border-cream-dark shadow-sm">
        <form method="GET" action="<?= BASE_URL ?>/raw-materials" class="flex gap-4 items-center">
            <div class="flex-grow">
                <input type="text" name="search" value="<?= e($search) ?>" placeholder="Buscar insumo por nombre..." 
                       class="w-full px-4 py-2.5 rounded-xl border border-cream-dark placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-accent text-sm">
            </div>
            <div>
                <button type="submit" class="bg-coffee-medium hover:bg-coffee-dark text-white font-semibold px-6 py-2.5 rounded-xl transition duration-200 text-sm">
                    Buscar 🔍
                </button>
            </div>
        </form>
    </div>

    <!-- Table List -->
    <div class="bg-white rounded-2xl border border-cream-dark overflow-hidden shadow-sm animate-fade-in">
        <?php if (empty($rawMaterials)): ?>
            <div class="p-12 text-center text-slate-400">
                <span class="text-5xl block mb-2">📦</span>
                <p>No se encontraron materias primas registradas.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-coffee-dark/5 text-coffee-dark border-b border-cream-dark text-xs uppercase font-bold tracking-wider">
                            <th class="p-4 pl-6">Insumo</th>
                            <th class="p-4">Unidad</th>
                            <th class="p-4 text-right">Precio Ref.</th>
                            <th class="p-4">Stock Actual</th>
                            <th class="p-4">Stock Mínimo</th>
                            <th class="p-4">Estado Alerta</th>
                            <th class="p-4 pr-6 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-cream-dark text-sm">
                        <?php foreach ($rawMaterials as $mat): ?>
                            <?php 
                            $isLowStock = ($mat['current_stock'] <= $mat['min_stock']);
                            ?>
                            <tr class="hover:bg-cream/10 transition">
                                <td class="p-4 pl-6 font-bold text-coffee-dark"><?= e($mat['name']) ?></td>
                                <td class="p-4 text-coffee-medium"><?= e($mat['unit']) ?></td>
                                <td class="p-4 text-right font-bold text-coffee-dark"><?= formatMoney($mat['price'] ?? 0) ?></td>
                                <td class="p-4 font-bold <?= $isLowStock ? 'text-rose-600' : 'text-slate-800' ?>">
                                    <?= number_format($mat['current_stock'], 2) ?>
                                </td>
                                <td class="p-4 text-slate-500 font-medium"><?= number_format($mat['min_stock'], 2) ?></td>
                                <td class="p-4">
                                    <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold shadow-sm <?= $isLowStock ? 'bg-rose-100 text-rose-800 border border-rose-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200' ?>">
                                        <?= $isLowStock ? '⚠️ Crítico' : '✅ Suficiente' ?>
                                    </span>
                                </td>
                                <td class="p-4 pr-6">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="<?= BASE_URL ?>/raw-materials/edit?id=<?= $mat['id'] ?>" class="bg-coffee-medium/10 hover:bg-coffee-medium/20 text-coffee-dark text-xs font-bold px-3 py-1.5 rounded-lg transition duration-200">
                                            ✏️ Editar
                                        </a>
                                        <form method="POST" action="<?= BASE_URL ?>/raw-materials/delete" onsubmit="return confirm('¿Seguro que deseas eliminar este insumo?');" class="inline">
                                            <input type="hidden" name="id" value="<?= $mat['id'] ?>">
                                            <button type="submit" class="bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold px-3 py-1.5 rounded-lg transition duration-200">
                                                🗑️ Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
