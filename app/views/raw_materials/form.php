<?php
$isEdit = !empty($rawMaterial);
?>
<div class="max-w-xl mx-auto space-y-6">
    <!-- Back Link -->
    <div>
        <a href="<?= BASE_URL ?>/raw-materials" class="text-sm font-semibold text-coffee-medium hover:text-coffee-dark flex items-center gap-1">
            ← Volver a Inventario
        </a>
    </div>

    <!-- Header -->
    <div>
        <h1 class="text-3xl font-heading font-extrabold text-coffee-dark">
            <?= $isEdit ? 'Editar Insumo' : 'Crear Insumo' ?>
        </h1>
        <p class="text-coffee-light">Ingresa la información detallada de la materia prima.</p>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-2xl border border-cream-dark p-6 sm:p-8 shadow-sm">
        <form action="<?= BASE_URL ?>/raw-materials/save" method="POST" class="space-y-6">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= $rawMaterial['id'] ?>">
            <?php endif; ?>

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-semibold text-coffee-dark">Nombre del Insumo / Ingrediente</label>
                <input type="text" id="name" name="name" value="<?= $isEdit ? e($rawMaterial['name']) : '' ?>" placeholder="Ej: Carne de res, Queso Cheddar" required
                       class="mt-1 w-full px-4 py-2.5 rounded-xl border border-cream-dark focus:outline-none focus:ring-2 focus:ring-accent text-sm">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Unit of Measure -->
                <div>
                    <label for="unit" class="block text-sm font-semibold text-coffee-dark">Unidad de Medida</label>
                    <input type="text" id="unit" name="unit" value="<?= $isEdit ? e($rawMaterial['unit']) : '' ?>" placeholder="Ej: un, gr, ml, lonchas" required
                           class="mt-1 w-full px-4 py-2.5 rounded-xl border border-cream-dark focus:outline-none focus:ring-2 focus:ring-accent text-sm">
                </div>

                <!-- Current Stock -->
                <div>
                    <label for="current_stock" class="block text-sm font-semibold text-coffee-dark">Stock Actual</label>
                    <input type="number" id="current_stock" name="current_stock" step="0.01" min="0" value="<?= $isEdit ? e($rawMaterial['current_stock']) : '0.00' ?>" required
                           class="mt-1 w-full px-4 py-2.5 rounded-xl border border-cream-dark focus:outline-none focus:ring-2 focus:ring-accent text-sm">
                </div>

                <!-- Min Stock -->
                <div>
                    <label for="min_stock" class="block text-sm font-semibold text-coffee-dark">Stock Mínimo</label>
                    <input type="number" id="min_stock" name="min_stock" step="0.01" min="0" value="<?= $isEdit ? e($rawMaterial['min_stock']) : '0.00' ?>" required
                           class="mt-1 w-full px-4 py-2.5 rounded-xl border border-cream-dark focus:outline-none focus:ring-2 focus:ring-accent text-sm">
                </div>
            </div>

            <!-- Submit Button -->
            <div class="border-t border-slate-100 pt-6 flex justify-end gap-3">
                <a href="<?= BASE_URL ?>/raw-materials" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold px-6 py-2.5 rounded-xl transition duration-200 text-sm">
                    Cancelar
                </a>
                <button type="submit" class="bg-accent hover:bg-accent-dark text-white font-bold px-8 py-2.5 rounded-xl transition duration-200 text-sm shadow-md">
                    Guardar Insumo
                </button>
            </div>
        </form>
    </div>
</div>
