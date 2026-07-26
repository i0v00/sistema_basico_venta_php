<?php
// ─── Inline SVG icon library for professional category icons ───────────────
$svgIcons = [
    'hamburger' => [
        'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9h18"/><path d="M3 15h18"/><path d="M5 6a7 7 0 0 1 14 0"/><rect x="3" y="9" width="18" height="6" rx="1"/><path d="M5 18h14a2 2 0 0 0 2-2v-1H3v1a2 2 0 0 0 2 2z"/></svg>',
        'label' => 'Hamburguesa',
        'bg'    => 'bg-amber-100',
        'text'  => 'text-amber-700',
    ],
    'fries' => [
        'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 11v8a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-8"/><path d="M5 11l2-7h10l2 7"/><line x1="8" y1="4" x2="8" y2="11"/><line x1="12" y1="3" x2="12" y2="11"/><line x1="16" y1="4" x2="16" y2="11"/></svg>',
        'label' => 'Salchipapas',
        'bg'    => 'bg-yellow-100',
        'text'  => 'text-yellow-700',
    ],
    'chicken' => [
        'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/><path d="M12 6S7 8 5 13c-1.5 4 1 8 5 8s7-2 8-6c1-3.5-1-7-6-9z"/><path d="M9 15c1 1.5 3 2 5 1"/></svg>',
        'label' => 'Pollo',
        'bg'    => 'bg-orange-100',
        'text'  => 'text-orange-700',
    ],
    'drink_cup' => [
        'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 2h14l-1.5 16.5a2 2 0 0 1-2 1.5h-7a2 2 0 0 1-2-1.5L5 2z"/><line x1="9" y1="2" x2="9.5" y2="6"/><line x1="15" y1="2" x2="14.5" y2="6"/><path d="M3 2h18"/></svg>',
        'label' => 'Vaso Refresco',
        'bg'    => 'bg-blue-100',
        'text'  => 'text-blue-700',
    ],
    'soda_bottle' => [
        'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3h8v3l2 4v10a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V10l2-4V3z"/><line x1="8" y1="3" x2="8" y2="6"/><line x1="16" y1="3" x2="16" y2="6"/><line x1="6" y1="14" x2="18" y2="14"/></svg>',
        'label' => 'Refresco',
        'bg'    => 'bg-cyan-100',
        'text'  => 'text-cyan-700',
    ],
    'package' => [
        'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="16.5" y1="9.4" x2="7.5" y2="4.21"/><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>',
        'label' => 'Envases/Caja',
        'bg'    => 'bg-brown-100',
        'text'  => 'text-amber-900',
    ],
    'dessert' => [
        'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 13 8 13s8-7.75 8-13a8 8 0 0 0-8-8z"/><circle cx="12" cy="10" r="3"/></svg>',
        'label' => 'Postres',
        'bg'    => 'bg-pink-100',
        'text'  => 'text-pink-700',
    ],
    'weekend' => [
        'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
        'label' => 'Fin de Semana',
        'bg'    => 'bg-purple-100',
        'text'  => 'text-purple-700',
    ],
    'coffee' => [
        'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V8z"/><line x1="6" y1="2" x2="6" y2="4"/><line x1="10" y1="2" x2="10" y2="4"/><line x1="14" y1="2" x2="14" y2="4"/></svg>',
        'label' => 'Café / Calientes',
        'bg'    => 'bg-stone-100',
        'text'  => 'text-stone-700',
    ],
    'pizza' => [
        'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2C6.477 2 2 6.477 2 12l10 10L22 12C22 6.477 17.523 2 12 2z"/><path d="M12 2v10"/><circle cx="9" cy="9" r="1" fill="currentColor"/><circle cx="14" cy="13" r="1" fill="currentColor"/></svg>',
        'label' => 'Pizza / Masa',
        'bg'    => 'bg-red-100',
        'text'  => 'text-red-700',
    ],
    'salad' => [
        'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 14s3-3 6-3 6 3 9 3 6-3 6-3"/><path d="M2 20h20"/><path d="M8 14V8a4 4 0 0 1 8 0v6"/></svg>',
        'label' => 'Ensaladas',
        'bg'    => 'bg-green-100',
        'text'  => 'text-green-700',
    ],
    'combo' => [
        'svg'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8"/><path d="M12 17v4"/><path d="M7 8h2"/><path d="M15 8h2"/><path d="M11 12h2"/></svg>',
        'label' => 'Combos',
        'bg'    => 'bg-indigo-100',
        'text'  => 'text-indigo-700',
    ],
];
?>

<div class="space-y-6 animate-fade-in">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-heading font-extrabold text-coffee-dark">Gestión de Categorías</h1>
            <p class="text-coffee-light">Agrega y administra categorías para tu menú de comida rápida</p>
        </div>
        <a href="<?= BASE_URL ?>/products" class="inline-flex items-center gap-2 bg-white border border-cream-dark text-coffee-dark hover:bg-cream px-5 py-3 rounded-xl font-bold shadow-sm transition duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Volver a Productos
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- ─── Add Category Form ─── -->
        <div class="bg-white p-6 rounded-2xl border border-cream-dark shadow-sm h-fit hover-card">
            <h2 class="font-heading font-extrabold text-lg text-coffee-dark mb-5 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-accent/15 text-accent flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </div>
                Nueva Categoría
            </h2>

            <form action="<?= BASE_URL ?>/categories/save" method="POST" class="space-y-5">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-coffee-dark mb-1">Nombre de la Categoría</label>
                    <input type="text" id="name" name="name" required placeholder="Ej: Hamburguesas, Bebidas..."
                           class="w-full px-4 py-2.5 rounded-xl border border-cream-dark focus:outline-none focus:ring-2 focus:ring-accent text-sm">
                </div>

                <!-- Icon hidden input -->
                <input type="hidden" id="icon" name="icon" value="hamburger">

                <!-- Icon Picker Grid -->
                <div>
                    <label class="block text-sm font-semibold text-coffee-dark mb-2">Icono de Categoría</label>
                    <div id="icon-picker-new" class="grid grid-cols-4 gap-2">
                        <?php foreach ($svgIcons as $key => $iconData): ?>
                        <button type="button"
                                onclick="selectIcon('<?= $key ?>', 'new')"
                                data-icon-key="<?= $key ?>"
                                title="<?= $iconData['label'] ?>"
                                class="icon-btn-new flex flex-col items-center gap-1 p-2 rounded-xl border-2 transition-all duration-150 hover:scale-105 group
                                       <?= $key === 'hamburger' ? 'border-accent bg-accent/10 selected-icon' : 'border-cream-dark hover:border-accent/50 hover:bg-cream/50' ?>">
                            <div class="w-8 h-8 flex items-center justify-center <?= $iconData['bg'] ?> <?= $iconData['text'] ?> rounded-lg [&_svg]:w-5 [&_svg]:h-5">
                                <?= $iconData['svg'] ?>
                            </div>
                            <span class="text-[9px] font-semibold text-coffee-medium leading-tight text-center"><?= $iconData['label'] ?></span>
                        </button>
                        <?php endforeach; ?>
                    </div>
                    <p class="text-xs text-coffee-light mt-2">
                        <span class="font-semibold text-accent" id="selected-icon-label-new">Hamburguesa</span> seleccionado
                    </p>
                </div>

                <!-- Submit -->
                <button type="submit"
                        class="w-full bg-gradient-to-r from-accent to-accent-dark hover:brightness-110 text-white font-bold py-3 rounded-xl shadow-md transition duration-200 flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    Guardar Categoría
                </button>
            </form>
        </div>

        <!-- ─── Categories Table ─── -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-cream-dark overflow-hidden shadow-sm hover-card">
            <div class="p-4 border-b border-cream-dark bg-coffee-dark/3 flex items-center justify-between">
                <h2 class="font-heading font-extrabold text-base text-coffee-dark flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-coffee-medium" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    Categorías Registradas
                </h2>
                <span class="text-xs font-bold text-coffee-medium bg-cream px-2.5 py-1 rounded-lg border border-cream-dark"><?= count($categories) ?> total</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-coffee-dark/5 text-coffee-dark border-b border-cream-dark text-xs uppercase font-bold tracking-wider">
                            <th class="p-4 pl-6">Icono</th>
                            <th class="p-4">Categoría</th>
                            <th class="p-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-cream-dark text-sm">
                        <?php foreach ($categories as $cat): ?>
                            <?php
                            $catIcon = $svgIcons[$cat['icon']] ?? null;
                            ?>
                            <tr class="hover:bg-cream/20 transition group">
                                <td class="p-4 pl-6">
                                    <?php if ($catIcon): ?>
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center <?= $catIcon['bg'] ?> <?= $catIcon['text'] ?> [&_svg]:w-5 [&_svg]:h-5 border border-current/20">
                                        <?= $catIcon['svg'] ?>
                                    </div>
                                    <?php else: ?>
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-cream border border-cream-dark text-2xl">
                                        <?= e($cat['icon']) ?>
                                    </div>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4 font-semibold text-coffee-dark"><?= e($cat['name']) ?></td>
                                <td class="p-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Edit Button -->
                                        <button type="button"
                                                onclick="openEditModal(<?= $cat['id'] ?>, '<?= addslashes(e($cat['name'])) ?>', '<?= e($cat['icon']) ?>')"
                                                class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-lg border transition bg-blue-50 hover:bg-blue-100 text-blue-700 border-blue-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                            Editar
                                        </button>
                                        <!-- Delete Form -->
                                        <form action="<?= BASE_URL ?>/categories/delete" method="POST"
                                              onsubmit="return confirm('¿Estás seguro? Todos los productos de esta categoría también se eliminarán.');"
                                              class="inline-block">
                                            <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                            <button type="submit" class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-lg border transition bg-rose-50 hover:bg-rose-100 text-rose-600 border-rose-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ─── Edit Category Modal ─────────────────────────────────────────── -->
<div id="edit-cat-modal" class="fixed inset-0 bg-coffee-dark/60 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl w-[95%] md:w-[25%] md:min-w-[320px] border border-cream-dark shadow-2xl overflow-hidden animate-fade-in">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-coffee-dark to-coffee-medium text-white p-5 flex items-center justify-between">
            <h4 class="font-heading font-extrabold text-base flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Editar Categoría
            </h4>
            <button onclick="closeEditModal()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/15 transition text-white font-bold text-lg">&times;</button>
        </div>

        <form action="<?= BASE_URL ?>/categories/update" method="POST" class="p-6 space-y-5">
            <input type="hidden" id="edit-cat-id" name="id" value="">
            <input type="hidden" id="edit-cat-icon" name="icon" value="hamburger">

            <!-- Name field -->
            <div>
                <label for="edit-cat-name" class="block text-sm font-semibold text-coffee-dark mb-1">Nombre de la Categoría</label>
                <input type="text" id="edit-cat-name" name="name" required
                       class="w-full px-4 py-2.5 rounded-xl border border-cream-dark focus:outline-none focus:ring-2 focus:ring-accent text-sm">
            </div>

            <!-- Icon Picker -->
            <div>
                <label class="block text-sm font-semibold text-coffee-dark mb-2">Icono de Categoría</label>
                <div id="icon-picker-edit" class="grid grid-cols-4 sm:grid-cols-6 gap-2">
                    <?php foreach ($svgIcons as $key => $iconData): ?>
                    <button type="button"
                            onclick="selectIcon('<?= $key ?>', 'edit')"
                            data-icon-key="<?= $key ?>"
                            title="<?= $iconData['label'] ?>"
                            class="icon-btn-edit flex flex-col items-center gap-1 p-2 rounded-xl border-2 transition-all duration-150 hover:scale-105
                                   border-cream-dark hover:border-accent/50 hover:bg-cream/50">
                        <div class="w-8 h-8 flex items-center justify-center <?= $iconData['bg'] ?> <?= $iconData['text'] ?> rounded-lg [&_svg]:w-5 [&_svg]:h-5">
                            <?= $iconData['svg'] ?>
                        </div>
                        <span class="text-[9px] font-semibold text-coffee-medium leading-tight text-center"><?= $iconData['label'] ?></span>
                    </button>
                    <?php endforeach; ?>
                </div>
                <p class="text-xs text-coffee-light mt-2">
                    Icono: <span class="font-semibold text-accent" id="selected-icon-label-edit">—</span>
                </p>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="closeEditModal()"
                        class="flex-1 bg-cream hover:bg-cream-dark border border-cream-dark text-coffee-dark font-bold py-2.5 rounded-xl transition text-sm">
                    Cancelar
                </button>
                <button type="submit"
                        class="flex-1 bg-gradient-to-r from-accent to-accent-dark hover:brightness-110 text-white font-bold py-2.5 rounded-xl transition text-sm flex items-center justify-center gap-2 shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// ─── Icon selection (shared for new & edit) ────────────────────────────
const svgLabels = <?= json_encode(array_map(fn($v) => $v['label'], $svgIcons)) ?>;

function selectIcon(key, context) {
    // Update hidden input
    const hiddenInput = context === 'new'
        ? document.getElementById('icon')
        : document.getElementById('edit-cat-icon');
    hiddenInput.value = key;

    // Update label
    document.getElementById('selected-icon-label-' + context).textContent = svgLabels[key] ?? key;

    // Update button highlights
    const btns = document.querySelectorAll('.icon-btn-' + context);
    btns.forEach(btn => {
        const isSelected = btn.getAttribute('data-icon-key') === key;
        btn.classList.toggle('border-accent', isSelected);
        btn.classList.toggle('bg-accent/10', isSelected);
        btn.classList.toggle('selected-icon', isSelected);
        btn.classList.toggle('border-cream-dark', !isSelected);
        btn.classList.toggle('hover:border-accent/50', !isSelected);
    });
}

// ─── Edit Modal ───────────────────────────────────────────────────────
function openEditModal(id, name, icon) {
    document.getElementById('edit-cat-id').value   = id;
    document.getElementById('edit-cat-name').value = name;
    document.getElementById('edit-cat-icon').value  = icon;

    // Pre-select the icon in the edit picker
    selectIcon(icon, 'edit');

    document.getElementById('edit-cat-modal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('edit-cat-modal').classList.add('hidden');
}

// Close on backdrop click
document.getElementById('edit-cat-modal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
</script>
