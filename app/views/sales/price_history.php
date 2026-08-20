<?php
// $products       = array of all products
// $priceHistories = [product_id => [price history rows...]]
?>
<div class="space-y-6 animate-slide-up">
    <!-- Header -->
    <div class="bg-white p-6 rounded-2xl border border-cream-dark shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-heading font-extrabold text-coffee-dark">💰 Precios Históricos</h1>
            <p class="text-coffee-light mt-0.5">Consulta y gestiona el historial y vigencia de precios de cada producto</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="<?= BASE_URL ?>/sales/history" class="inline-flex items-center gap-1.5 bg-coffee-dark hover:bg-coffee-medium text-white px-4 py-2.5 rounded-xl font-bold text-sm shadow-sm transition">
                <span>📋</span> Historial Ventas
            </a>
            <a href="<?= BASE_URL ?>/products" class="inline-flex items-center gap-1.5 bg-white border border-cream-dark text-coffee-dark hover:bg-cream px-4 py-2.5 rounded-xl font-bold text-sm shadow-sm transition">
                <span>🍔</span> Gestión Productos
            </a>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-white p-4 rounded-2xl border border-cream-dark shadow-sm">
        <form method="GET" action="<?= BASE_URL ?>/sales/price-history" class="flex gap-3 items-center">
            <input type="text" name="search" value="<?= e($search) ?>" placeholder="Buscar producto por nombre..."
                   class="flex-1 px-4 py-2.5 rounded-xl border border-cream-dark text-sm focus:outline-none focus:ring-2 focus:ring-accent">
            <button type="submit" class="bg-coffee-medium hover:bg-coffee-dark text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition duration-200">
                🔍 Buscar
            </button>
        </form>
    </div>

    <!-- Products Grid -->
    <?php if (empty($products)): ?>
        <div class="bg-white border border-cream-dark rounded-2xl p-12 text-center text-slate-400 shadow-sm">
            <span class="text-5xl block mb-2">🍔</span>
            <p>No se encontraron productos.</p>
        </div>
    <?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
        <?php foreach ($products as $prod):
            $history = $priceHistories[$prod['id']] ?? [];
            $priceCount = count($history);
            $latestPrice = !empty($history) ? (float)end($history)['price'] : (float)$prod['price'];
        ?>
        <div class="bg-white rounded-2xl border border-cream-dark shadow-sm flex flex-col hover-card animate-fade-in overflow-hidden">
            <!-- Product image -->
            <div class="h-36 bg-cream-dark relative flex items-center justify-center overflow-hidden">
                <?php if (!empty($prod['image']) && file_exists(__DIR__ . '/../../../uploads/products/' . $prod['image'])): ?>
                    <img src="<?= BASE_URL ?>/uploads/products/<?= e($prod['image']) ?>" alt="<?= e($prod['name']) ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <div class="w-full h-full bg-gradient-to-br from-cream to-cream-dark flex items-center justify-center text-4xl select-none">🍔</div>
                <?php endif; ?>
                <span class="absolute top-2 right-2 bg-coffee-dark/90 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-full">
                    <?= $priceCount ?> precio<?= $priceCount !== 1 ? 's' : '' ?>
                </span>
            </div>

            <div class="p-4 flex flex-col gap-3 flex-1">
                <div>
                    <span class="text-[10px] font-mono font-bold text-coffee-medium/60 uppercase tracking-wider"><?= e($prod['code']) ?></span>
                    <h3 class="font-heading font-extrabold text-coffee-dark text-sm leading-tight"><?= e($prod['name']) ?></h3>
                    <p class="text-xs text-slate-400 mt-0.5"><?= e($prod['category_icon']) ?> <?= e($prod['category_name']) ?></p>
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-slate-400 font-medium block">Precio actual</span>
                        <span class="text-xl font-extrabold text-emerald-700"><?= formatMoney($latestPrice) ?></span>
                    </div>
                    <?php if ($priceCount > 1): ?>
                        <span class="text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full">
                            <?= $priceCount - 1 ?> cambio<?= ($priceCount - 1) !== 1 ? 's' : '' ?>
                        </span>
                    <?php endif; ?>
                </div>
                <button type="button"
                        onclick="openPriceModal(<?= $prod['id'] ?>, '<?= e(addslashes($prod['name'])) ?>')"
                        class="w-full bg-accent hover:bg-accent-dark text-white font-bold text-sm py-2 rounded-xl transition duration-200 active:scale-95">
                    💰 Ver / Gestionar Precios
                </button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- ═══════════ MODAL PRINCIPAL ═══════════ -->
<div id="price-modal" class="fixed inset-0 z-[99999] flex items-center justify-center p-4 hidden" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-coffee-dark/70 backdrop-blur-sm" onclick="closePriceModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full sm:w-[40%] sm:max-w-[40%] max-h-[90vh] flex flex-col overflow-hidden animate-slide-up">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-coffee-dark to-coffee-medium p-5 text-white flex items-center justify-between shrink-0">
            <div>
                <h2 class="font-heading font-extrabold text-lg leading-tight" id="modal-product-name">Producto</h2>
                <p class="text-white/60 text-xs mt-0.5">Historial de Precios · Fechas en DD/MM/AAAA</p>
            </div>
            <button onclick="closePriceModal()" class="w-8 h-8 rounded-full bg-white/15 hover:bg-white/30 flex items-center justify-center transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-5 overflow-y-auto flex-1 space-y-5">
            <div id="modal-toast" class="hidden p-3 rounded-xl text-sm font-semibold"></div>

            <!-- History Table -->
            <div>
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-coffee-medium mb-2">Historial registrado</h3>
                <div class="overflow-x-auto rounded-xl border border-cream-dark">
                    <table class="w-full text-sm">
                        <thead class="bg-cream text-xs font-extrabold text-coffee-medium uppercase tracking-wide">
                            <tr>
                                <th class="px-4 py-2.5 text-left">Vigente desde</th>
                                <th class="px-4 py-2.5 text-left">Precio</th>
                                <th class="px-4 py-2.5 text-left">Vigente hasta</th>
                                <th class="px-4 py-2.5 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="history-tbody" class="divide-y divide-cream-dark"></tbody>
                    </table>
                </div>
            </div>

            <!-- Add new price -->
            <div class="bg-cream/40 border border-cream-dark rounded-xl p-4 space-y-3">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-coffee-medium">Agregar nuevo precio</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-coffee-dark mb-1">Precio (Bs.)</label>
                        <input type="number" id="new-price" step="0.01" min="0.01" placeholder="0.00"
                               class="w-full px-3 py-2 rounded-xl border border-cream-dark text-sm focus:outline-none focus:ring-2 focus:ring-accent bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-coffee-dark mb-1">📅 Vigente desde</label>
                        <input type="date" id="new-date" value="<?= date('Y-m-d') ?>"
                               class="w-full px-3 py-2 rounded-xl border border-cream-dark text-sm focus:outline-none focus:ring-2 focus:ring-accent bg-white font-medium">
                    </div>
                </div>
                <button onclick="addNewPrice()" class="w-full bg-accent hover:bg-accent-dark text-white font-bold py-2.5 rounded-xl text-sm transition active:scale-95">
                    ➕ Registrar Nuevo Precio
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════ MODAL EDITAR ═══════════ -->
<div id="edit-modal" class="fixed inset-0 z-[999999] flex items-center justify-center p-4 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeEditModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full sm:w-[40%] sm:max-w-[40%] p-6 space-y-4 animate-slide-up">
        <h3 class="font-heading font-extrabold text-coffee-dark text-base">✏️ Editar Precio Histórico</h3>
        <input type="hidden" id="edit-history-id">
        <input type="hidden" id="edit-product-id">
        <div>
            <label class="block text-xs font-semibold text-coffee-dark mb-1">Precio (Bs.)</label>
            <input type="number" id="edit-price" step="0.01" min="0.01"
                   class="w-full px-3 py-2 rounded-xl border border-cream-dark text-sm focus:outline-none focus:ring-2 focus:ring-accent bg-white">
        </div>
        <div>
            <label class="block text-xs font-semibold text-coffee-dark mb-1">📅 Vigente desde</label>
            <input type="date" id="edit-date"
                   class="w-full px-3 py-2 rounded-xl border border-cream-dark text-sm focus:outline-none focus:ring-2 focus:ring-accent bg-white font-medium">
        </div>
        <div class="flex gap-3 pt-1">
            <button onclick="closeEditModal()" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold py-2 rounded-xl text-sm transition">Cancelar</button>
            <button onclick="submitEdit()" class="flex-1 bg-accent hover:bg-accent-dark text-white font-bold py-2 rounded-xl text-sm transition">Guardar</button>
        </div>
    </div>
</div>

<script>
const ALL_HISTORIES = <?= json_encode($priceHistories) ?>;
let currentProductId = null;

function fmtDate(ymd) {
    if (!ymd) return '—';
    const [y, m, d] = ymd.split('-');
    return `${d}/${m}/${y}`;
}
function fmtMoney(v) { return 'Bs. ' + parseFloat(v).toFixed(2); }

function openPriceModal(productId, productName) {
    currentProductId = productId;
    document.getElementById('modal-product-name').textContent = productName;
    document.getElementById('new-price').value = '';
    document.getElementById('new-date').value = '<?= date('Y-m-d') ?>';
    hideToast();
    renderHistory(ALL_HISTORIES[productId] || []);
    document.getElementById('price-modal').classList.remove('hidden');
}
function closePriceModal() {
    document.getElementById('price-modal').classList.add('hidden');
    currentProductId = null;
}

function renderHistory(history) {
    const tbody = document.getElementById('history-tbody');
    tbody.innerHTML = '';
    if (!history || history.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="px-4 py-6 text-center text-slate-400 text-xs">Sin historial de precios.</td></tr>';
        return;
    }
    history.forEach((row, idx) => {
        const isOldest = (idx === 0);
        let rangeTo = '<span class="bg-emerald-100 text-emerald-800 border border-emerald-200 px-2 py-0.5 rounded-full font-bold text-[10px]">✅ Vigente</span>';
        if (idx < history.length - 1) {
            const nextDate = new Date(history[idx + 1].effective_date + 'T00:00:00');
            nextDate.setDate(nextDate.getDate() - 1);
            rangeTo = fmtDate(nextDate.toISOString().split('T')[0]);
        }
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-cream/40 transition-colors duration-100';
        tr.innerHTML = `
            <td class="px-4 py-3 font-semibold text-coffee-dark text-xs">
                ${isOldest ? '<span class="text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-1.5 py-0.5 rounded mr-1">Base</span>' : ''}
                ${fmtDate(row.effective_date)}
            </td>
            <td class="px-4 py-3 font-extrabold text-emerald-700">${fmtMoney(row.price)}</td>
            <td class="px-4 py-3 text-xs text-slate-500">${rangeTo}</td>
            <td class="px-4 py-3 text-center">
                <div class="flex items-center justify-center gap-1">
                    <button onclick="openEdit(${row.id}, ${row.price}, '${row.effective_date}', ${isOldest})"
                            title="Editar precio"
                            class="text-xs font-bold text-coffee-medium hover:text-accent px-2 py-1 rounded-lg hover:bg-cream transition">✏️</button>
                    ${isOldest
                        ? '<span class="text-[10px] text-slate-300 font-semibold px-1" title="El precio base inicial no se puede eliminar">🔒</span>'
                        : `<button onclick="deletePrice(${row.id})"
                                title="Eliminar precio"
                                class="text-xs font-bold text-rose-500 hover:text-rose-700 px-2 py-1 rounded-lg hover:bg-rose-50 transition">🗑️</button>`
                    }
                </div>
            </td>`;
        tbody.appendChild(tr);
    });
}

function addNewPrice() {
    const ymd   = document.getElementById('new-date').value.trim();
    const price = parseFloat(document.getElementById('new-price').value);
    if (!ymd) { showToast('Selecciona una fecha válida en el calendario.', false); return; }
    if (!price || price <= 0) { showToast('Ingresa un precio válido.', false); return; }
    postAction('<?= BASE_URL ?>/products/price-history/save', {
        product_id: currentProductId, price, effective_date: ymd
    }, data => {
        if (data.success) {
            updateHistory(data.history);
            renderHistory(data.history);
            document.getElementById('new-price').value = '';
            document.getElementById('new-date').value = '<?= date('Y-m-d') ?>';
            showToast(data.message, true);
        } else showToast(data.message, false);
    });
}

function openEdit(histId, price, ymd, isBase = false) {
    document.getElementById('edit-history-id').value = histId;
    document.getElementById('edit-product-id').value  = currentProductId;
    document.getElementById('edit-price').value = price;
    const dateInput = document.getElementById('edit-date');
    if (isBase) {
        dateInput.value = ymd;
        dateInput.disabled = true;
        dateInput.title = 'La fecha del precio base inicial no se modifica';
    } else {
        dateInput.value = ymd;
        dateInput.disabled = false;
        dateInput.title = '';
    }
    document.getElementById('edit-modal').classList.remove('hidden');
}
function closeEditModal() { document.getElementById('edit-modal').classList.add('hidden'); }
function submitEdit() {
    const dateInput = document.getElementById('edit-date');
    const isBase    = dateInput.disabled;
    const ymd       = isBase ? '2000-01-01' : dateInput.value.trim();
    const price     = parseFloat(document.getElementById('edit-price').value);

    if (!isBase && !ymd) { showToast('Selecciona una fecha válida en el calendario.', false); closeEditModal(); return; }
    if (!price || price <= 0) { showToast('Ingresa un precio válido.', false); closeEditModal(); return; }

    postAction('<?= BASE_URL ?>/products/price-history/save', {
        product_id: document.getElementById('edit-product-id').value,
        history_id: document.getElementById('edit-history-id').value,
        price, effective_date: ymd || '2000-01-01'
    }, data => {
        closeEditModal();
        if (data.success) { updateHistory(data.history); renderHistory(data.history); showToast(data.message, true); }
        else showToast(data.message, false);
    });
}

function deletePrice(histId) {
    if (!confirm('¿Eliminar este precio histórico?')) return;
    postAction('<?= BASE_URL ?>/products/price-history/delete', {
        history_id: histId, product_id: currentProductId
    }, data => {
        if (data.success) { updateHistory(data.history); renderHistory(data.history); showToast(data.message, true); }
        else showToast(data.message, false);
    });
}

function postAction(url, body, cb) {
    const fd = new FormData();
    Object.entries(body).forEach(([k,v]) => fd.append(k, v));
    fetch(url, { method:'POST', body: fd }).then(r => r.json()).then(cb).catch(() => showToast('Error de conexión.', false));
}

function updateHistory(newHistory) { ALL_HISTORIES[currentProductId] = newHistory; }

function showToast(msg, ok) {
    const t = document.getElementById('modal-toast');
    t.textContent = msg;
    t.className = 'p-3 rounded-xl text-sm font-semibold ' +
        (ok ? 'bg-emerald-50 text-emerald-800 border border-emerald-200'
            : 'bg-rose-50 text-rose-700 border border-rose-200');
    t.classList.remove('hidden');
    setTimeout(() => t.classList.add('hidden'), 4000);
}
function hideToast() { document.getElementById('modal-toast').classList.add('hidden'); }

// Auto-format date inputs
function autoFmt(el) {
    el.addEventListener('input', function () {
        let v = this.value.replace(/\D/g,'').slice(0,8);
        if (v.length > 4) v = v.slice(0,2)+'/'+v.slice(2,4)+'/'+v.slice(4);
        else if (v.length > 2) v = v.slice(0,2)+'/'+v.slice(2);
        this.value = v;
    });
}
autoFmt(document.getElementById('new-date'));
autoFmt(document.getElementById('edit-date'));
document.addEventListener('keydown', e => { if (e.key==='Escape') { closePriceModal(); closeEditModal(); } });
</script>
