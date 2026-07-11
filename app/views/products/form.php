<?php
$isEdit = !empty($product);
?>
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Breadcrumbs/Back -->
    <div>
        <a href="<?= BASE_URL ?>/products" class="text-sm font-semibold text-coffee-medium hover:text-coffee-dark flex items-center gap-1">
            ← Volver a Productos
        </a>
    </div>

    <!-- Header -->
    <div>
        <h1 class="text-3xl font-heading font-extrabold text-coffee-dark">
            <?= $isEdit ? 'Editar Producto' : 'Crear Producto' ?>
        </h1>
        <p class="text-coffee-light">Ingresa la información básica y define la receta de materias primas.</p>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-2xl border border-cream-dark p-6 sm:p-8 shadow-sm">
        <form action="<?= BASE_URL ?>/products/save" method="POST" enctype="multipart/form-data" class="space-y-6">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= $product['id'] ?>">
                <input type="hidden" name="existing_image" value="<?= e($product['image']) ?>">
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Code -->
                <div>
                    <label for="code" class="block text-sm font-semibold text-coffee-dark">Código Único de Producto</label>
                    <input type="text" id="code" name="code" value="<?= $isEdit ? e($product['code']) : '' ?>" placeholder="Ej: HAM-SUP" required
                           class="mt-1 w-full px-4 py-2.5 rounded-xl border border-cream-dark focus:outline-none focus:ring-2 focus:ring-accent text-sm font-semibold uppercase">
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-coffee-dark">Nombre del Producto</label>
                    <input type="text" id="name" name="name" value="<?= $isEdit ? e($product['name']) : '' ?>" required
                           class="mt-1 w-full px-4 py-2.5 rounded-xl border border-cream-dark focus:outline-none focus:ring-2 focus:ring-accent text-sm">
                </div>

                <!-- Price -->
                <div>
                    <label for="price" class="block text-sm font-semibold text-coffee-dark">Precio de Venta ($)</label>
                    <input type="number" id="price" name="price" step="0.01" min="0.01" value="<?= $isEdit ? e($product['price']) : '' ?>" required
                           class="mt-1 w-full px-4 py-2.5 rounded-xl border border-cream-dark focus:outline-none focus:ring-2 focus:ring-accent text-sm">
                </div>

                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-sm font-semibold text-coffee-dark">Categoría</label>
                    <select id="category_id" name="category_id" required
                            class="mt-1 w-full px-4 py-2.5 rounded-xl border border-cream-dark focus:outline-none focus:ring-2 focus:ring-accent text-sm">
                        <option value="">Selecciona una categoría</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($isEdit && $product['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                <?= e($cat['icon']) ?> <?= e($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Image -->
                <div>
                    <label for="image" class="block text-sm font-semibold text-coffee-dark">Imagen del Producto</label>
                    <input type="file" id="image" name="image" accept="image/*"
                           class="mt-1 w-full px-4 py-1.5 rounded-xl border border-cream-dark file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-coffee-medium/10 file:text-coffee-dark hover:file:bg-coffee-medium/20 text-xs text-slate-500">
                    <?php if ($isEdit && !empty($product['image'])): ?>
                        <span class="text-xs text-slate-400 mt-1 block">Imagen actual: <strong><?= e($product['image']) ?></strong></span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-semibold text-coffee-dark">Descripción</label>
                <textarea id="description" name="description" rows="3"
                          class="mt-1 w-full px-4 py-2.5 rounded-xl border border-cream-dark focus:outline-none focus:ring-2 focus:ring-accent text-sm" 
                          placeholder="Escribe detalles del producto..."><?= $isEdit ? e($product['description']) : '' ?></textarea>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center gap-6 border-t border-slate-100 pt-6">
                <!-- Active Toggle -->
                <div class="flex items-center">
                    <input type="checkbox" id="active" name="active" value="1" <?= (!$isEdit || $product['active']) ? 'checked' : '' ?>
                           class="w-4 h-4 text-accent border-cream-dark rounded focus:ring-accent">
                    <label for="active" class="ml-2 text-sm font-semibold text-coffee-dark">Producto Activo (Visible en POS)</label>
                </div>

                <!-- Use Recipe Toggle -->
                <div class="flex items-center">
                    <input type="checkbox" id="use_recipe" name="use_recipe" value="1" <?= ($isEdit && $product['use_recipe']) ? 'checked' : '' ?>
                           class="w-4 h-4 text-accent border-cream-dark rounded focus:ring-accent">
                    <label for="use_recipe" class="ml-2 text-sm font-semibold text-coffee-dark font-medium">Controlar Inventario / Receta</label>
                </div>
            </div>

            <!-- Recipe Ingredient list (shows dynamic if use_recipe is checked) -->
            <div id="recipe-section" class="<?= ($isEdit && $product['use_recipe']) ? '' : 'hidden' ?> bg-cream/30 p-6 rounded-2xl border border-cream-dark/60 space-y-4">
                <div>
                    <h3 class="font-heading font-bold text-lg text-coffee-dark">Fórmula / Receta</h3>
                    <p class="text-xs text-coffee-light">Ingresa la cantidad exacta de ingredientes que se descontarán por cada unidad vendida de este producto.</p>
                </div>

                <?php if (empty($rawMaterials)): ?>
                    <p class="text-xs text-rose-600 bg-rose-50 p-3 rounded-lg border border-rose-200">
                        ⚠️ Primero debes registrar <a href="/raw-materials" class="font-bold underline">Materias Primas</a> en el inventario para poder armar una receta.
                    </p>
                <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php foreach ($rawMaterials as $mat): ?>
                            <?php 
                            $qty = $recipeMap[$mat['id']] ?? 0;
                            ?>
                            <div class="bg-white border border-cream-dark px-4 py-3 rounded-xl flex items-center justify-between shadow-sm">
                                <div>
                                    <span class="block text-sm font-bold text-coffee-dark"><?= e($mat['name']) ?></span>
                                    <span class="text-xs text-slate-400">Unidad: <?= e($mat['unit']) ?></span>
                                </div>
                                <div class="w-24">
                                    <input type="number" name="recipe[<?= $mat['id'] ?>]" step="0.01" min="0" value="<?= $qty ?>"
                                           class="w-full text-right px-2.5 py-1.5 border border-cream-dark rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-accent">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Submit Button -->
            <div class="border-t border-slate-100 pt-6 flex justify-end gap-3">
                <a href="<?= BASE_URL ?>/products" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-6 py-2.5 rounded-xl transition duration-200 text-sm">
                    Cancelar
                </a>
                <button type="submit" class="bg-accent hover:bg-accent-dark text-white font-bold px-8 py-2.5 rounded-xl transition duration-200 text-sm shadow-md">
                    Guardar Producto
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Show / Hide Recipe box dynamically
    const useRecipeCheckbox = document.getElementById('use_recipe');
    const recipeSection = document.getElementById('recipe-section');
    if (useRecipeCheckbox && recipeSection) {
        useRecipeCheckbox.addEventListener('change', function() {
            if (this.checked) {
                recipeSection.classList.remove('hidden');
            } else {
                recipeSection.classList.add('hidden');
            }
        });
    }
</script>
