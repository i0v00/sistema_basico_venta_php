<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-heading font-extrabold text-coffee-dark">Gestión de Productos</h1>
            <p class="text-coffee-light">Crea, edita y administra el menú de comidas y bebidas</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="<?= BASE_URL ?>/sales/price-history" class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white px-5 py-3 rounded-xl font-bold shadow-md transition duration-200">
                <span>💰</span> Precios Históricos
            </a>
            <a href="<?= BASE_URL ?>/categories" class="inline-flex items-center gap-2 bg-white border border-cream-dark text-coffee-dark hover:bg-cream hover:text-accent px-5 py-3 rounded-xl font-bold shadow-sm transition duration-200">
                <span>📁</span> Categorías
            </a>
            <a href="<?= BASE_URL ?>/products/create" class="inline-flex items-center gap-2 bg-accent hover:bg-accent-dark text-white px-5 py-3 rounded-xl font-bold shadow-md transition duration-200">
                <span>➕</span> Nuevo Producto
            </a>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white p-4 rounded-2xl border border-cream-dark shadow-sm">
        <form method="GET" action="<?= BASE_URL ?>/products" class="flex flex-col md:flex-row gap-4 items-center">
            <!-- Search -->
            <div class="w-full md:flex-grow">
                <input type="text" name="search" value="<?= e($search) ?>" placeholder="Buscar por nombre de producto..." 
                       class="w-full px-4 py-2.5 rounded-xl border border-cream-dark placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-accent text-sm">
            </div>

            <!-- Category -->
            <div class="w-full md:w-64">
                <select name="category_id" class="w-full px-4 py-2.5 rounded-xl border border-cream-dark focus:outline-none focus:ring-2 focus:ring-accent text-sm">
                    <option value="">Todas las Categorías</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $categoryId == $cat['id'] ? 'selected' : '' ?>>
                            <?= e($cat['icon']) ?> <?= e($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Submit -->
            <div class="w-full md:w-auto">
                <button type="submit" class="w-full bg-coffee-medium hover:bg-coffee-dark text-white font-semibold px-6 py-2.5 rounded-xl transition duration-200 text-sm">
                    Filtrar 🔍
                </button>
            </div>
        </form>
    </div>

    <!-- Grid Products -->
    <?php if (empty($products)): ?>
        <div class="bg-white border border-cream-dark rounded-2xl p-12 text-center text-slate-400 shadow-sm">
            <span class="text-5xl block mb-2">🍔</span>
            <p>No se encontraron productos en el sistema.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php foreach ($products as $prod): ?>
                <div class="bg-white rounded-2xl border border-cream-dark overflow-hidden flex flex-col justify-between hover-card animate-fade-in">
                    <!-- Image -->
                    <div class="h-44 bg-cream-dark relative flex items-center justify-center text-slate-400 overflow-hidden">
                        <?php if (!empty($prod['image']) && file_exists(__DIR__ . '/../../../uploads/products/' . $prod['image'])): ?>
                            <img src="<?= BASE_URL ?>/uploads/products/<?= e($prod['image']) ?>" alt="<?= e($prod['name']) ?>" class="w-full h-full object-cover hover:scale-105 transition duration-500">
                        <?php else: ?>
                            <div class="w-full h-full bg-gradient-to-br from-cream to-cream-dark flex flex-col items-center justify-center text-coffee-medium/40 p-4 select-none">
                                <div class="w-16 h-16 rounded-full bg-white/90 flex items-center justify-center shadow-md text-3xl mb-1 transition duration-300 hover:scale-110">🍔</div>
                                <span class="text-[9px] uppercase font-extrabold tracking-widest text-coffee-medium/60 font-mono">Sin Imagen</span>
                            </div>
                        <?php endif; ?>

                        <!-- Active Badge -->
                        <span class="absolute top-3 right-3 text-xs px-2.5 py-1 rounded-full font-bold shadow-sm <?= $prod['active'] ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-rose-100 text-rose-800 border border-rose-200' ?>">
                            <?= $prod['active'] ? 'Activo' : 'Inactivo' ?>
                        </span>

                        <!-- Category Badge -->
                        <span class="absolute bottom-3 left-3 bg-coffee-dark/85 backdrop-blur-sm text-white text-xs px-2.5 py-1 rounded-full font-medium">
                            <?= e($prod['category_icon']) ?> <?= e($prod['category_name']) ?>
                        </span>
                    </div>

                    <!-- Details -->
                    <div class="p-5 flex-grow flex flex-col justify-between">
                        <div class="space-y-1">
                            <h3 class="font-heading font-extrabold text-base text-coffee-dark tracking-tight leading-tight flex flex-wrap items-center gap-1.5">
                                <span class="bg-coffee-dark/10 text-coffee-dark text-[10px] px-1.5 py-0.5 rounded font-mono font-bold tracking-wider uppercase" title="Código de producto"><?= e($prod['code']) ?></span>
                                <span><?= e($prod['name']) ?></span>
                            </h3>
                            <p class="text-xs text-slate-500 line-clamp-2 h-8 leading-relaxed"><?= e($prod['description']) ?></p>
                        </div>

                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-lg font-extrabold text-coffee-dark"><?= formatMoney($prod['price']) ?></span>
                            
                            <?php if ($prod['use_recipe']): ?>
                                <span class="text-xs font-semibold text-orange-700 bg-orange-50 px-2 py-0.5 rounded border border-orange-200 flex items-center gap-1" title="Este producto descuenta materia prima">
                                    📦 Receta
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="p-4 border-t border-slate-100 bg-cream/20 flex gap-2">
                        <a href="<?= BASE_URL ?>/products/edit?id=<?= $prod['id'] ?>" class="flex-1 text-center bg-coffee-medium/10 hover:bg-coffee-medium/20 text-coffee-dark text-xs font-bold py-2 rounded-lg transition duration-200">
                            ✏️ Editar
                        </a>
                        <form method="POST" action="<?= BASE_URL ?>/products/delete" onsubmit="return confirm('¿Seguro que deseas eliminar este producto?');" class="flex-1">
                            <input type="hidden" name="id" value="<?= $prod['id'] ?>">
                            <button type="submit" class="w-full bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold py-2 rounded-lg transition duration-200">
                                🗑️ Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
