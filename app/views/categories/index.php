<div class="space-y-6 animate-fade-in">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-heading font-extrabold text-coffee-dark">Gestión de Categorías</h1>
            <p class="text-coffee-light">Agrega y administra categorías para tu menú de comida rápida</p>
        </div>
        <div>
            <a href="<?= BASE_URL ?>/products" class="inline-flex items-center gap-2 bg-white border border-cream-dark text-coffee-dark hover:bg-cream px-5 py-3 rounded-xl font-bold shadow-sm transition duration-200">
                <span>↩️</span> Volver a Productos
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Add Category Form -->
        <div class="bg-white p-6 rounded-2xl border border-cream-dark shadow-sm h-fit hover-card">
            <h2 class="font-heading font-extrabold text-lg text-coffee-dark mb-4">Nueva Categoría</h2>
            
            <form action="<?= BASE_URL ?>/categories/save" method="POST" class="space-y-4">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-coffee-dark">Nombre de la Categoría</label>
                    <input type="text" id="name" name="name" required placeholder="Ej: Bebidas, Hamburguesas..."
                           class="mt-1 w-full px-4 py-2.5 rounded-xl border border-cream-dark focus:outline-none focus:ring-2 focus:ring-accent text-sm">
                </div>

                <!-- Icon/Emoji picker -->
                <div>
                    <label for="icon" class="block text-sm font-semibold text-coffee-dark">Icono / Emoji</label>
                    <p class="text-xs text-coffee-light mb-1">Puedes escribir o pegar cualquier emoji/icono desde tu celular.</p>
                    <div class="flex flex-col sm:flex-row gap-2 mt-1">
                        <input type="text" id="icon" name="icon" value="🍔" required
                               class="w-full sm:w-28 px-3 py-2.5 text-center text-xl rounded-xl border border-cream-dark focus:outline-none focus:ring-2 focus:ring-accent">
                        <div class="flex-grow flex flex-wrap gap-1.5 p-2 bg-cream rounded-xl border border-cream-dark/50 items-center justify-center">
                            <?php foreach (['🍔', '🥤', '🍦', '🍟', '🍕', '🥗', '🌭', '🧁', '🍩', '🍗', '☕', '🍺'] as $emoji): ?>
                                <button type="button" onclick="document.getElementById('icon').value = '<?= $emoji ?>'"
                                        class="hover:scale-125 transition duration-150 text-lg p-0.5 focus:outline-none">
                                    <?= $emoji ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full bg-accent hover:bg-accent-dark text-white font-bold py-3 rounded-xl shadow-md transition duration-200">
                    Guardar Categoría 📁
                </button>
            </form>
        </div>

        <!-- Categories Table List -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-cream-dark overflow-hidden shadow-sm hover-card">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-coffee-dark/5 text-coffee-dark border-b border-cream-dark text-xs uppercase font-bold tracking-wider">
                            <th class="p-4 pl-6">Icono</th>
                            <th class="p-4">Categoría</th>
                            <th class="p-4 pr-6 text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-cream-dark text-sm">
                        <?php foreach ($categories as $cat): ?>
                            <tr class="hover:bg-cream/10 transition">
                                <td class="p-4 pl-6 text-2xl"><?= e($cat['icon']) ?></td>
                                <td class="p-4 font-semibold text-coffee-dark"><?= e($cat['name']) ?></td>
                                <td class="p-4 pr-6 text-center">
                                    <form action="<?= BASE_URL ?>/categories/delete" method="POST" 
                                          onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta categoría? Si la eliminas, todos los productos dentro de ella se borrarán.');" 
                                          class="inline-block">
                                        <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                        <button type="submit" class="text-rose-600 hover:text-rose-800 font-bold text-xs bg-rose-50 hover:bg-rose-100 px-3 py-1.5 rounded-lg border border-rose-200 transition">
                                            Eliminar 🗑️
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
