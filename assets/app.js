// ═══════════════════════════════════════════════════════════
//  Duke's Cakes — POS Engine v3
//  Uses POS_PRODUCTS, POS_BASE, POS_TRACK_RM from the page
// ═══════════════════════════════════════════════════════════

// ── State ────────────────────────────────────────────────────
let posCart     = [];
let posCatId    = null;     // null = all
let mobCartOpen = false;

// ── Boot ─────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    posRenderGrid();
    posRenderCart();
});

// ═══════════════════════════════════════════════════════════
//  PRODUCT GRID
// ═══════════════════════════════════════════════════════════

function posSelectCat(catId) {
    posCatId = catId;

    document.querySelectorAll('.cat-pill').forEach(btn => {
        btn.classList.remove('active');
        btn.classList.add('bg-white', 'text-coffee-dark', 'border-cream-dark');
    });

    const active = catId === null
        ? document.getElementById('cat-all')
        : document.getElementById('cat-' + catId);

    if (active) {
        active.classList.add('active');
        active.classList.remove('bg-white', 'text-coffee-dark', 'border-cream-dark');
    }
    posRenderGrid();
}

function posFilter() { posRenderGrid(); }

function posRenderGrid() {
    const grid  = document.getElementById('pos-grid');
    const empty = document.getElementById('pos-empty');
    if (!grid) return;

    const q = (document.getElementById('pos-search')?.value || '').toLowerCase().trim();

    const list = POS_PRODUCTS.filter(p => {
        if (posCatId !== null && parseInt(p.category_id) !== posCatId) return false;
        if (q && !p.name.toLowerCase().includes(q) && !(p.code || '').toLowerCase().includes(q)) return false;
        return true;
    });

    grid.innerHTML = '';

    if (!list.length) {
        empty.classList.remove('hidden');
        empty.classList.add('flex');
        grid.classList.add('hidden');
        return;
    }
    empty.classList.add('hidden');
    empty.classList.remove('flex');
    grid.classList.remove('hidden');

    list.forEach((p, i) => {
        const card = document.createElement('div');
        card.className = 'prod-card relative group bg-white rounded-2xl border border-cream-dark overflow-hidden flex flex-col cursor-pointer select-none shadow-sm hover:shadow-md hover:-translate-y-0.5 hover:border-accent/50 transition-all duration-250';
        card.style.animationDelay = (i * 25) + 'ms';
        card.onclick = ev => posAddToCart(p.id, ev, card);

        /* Image or placeholder (matches /products admin style) */
        const imgBlock = p.image
            ? `<div class="h-28 overflow-hidden bg-cream-dark">
                   <img src="${POS_BASE}/uploads/products/${p.image}"
                        alt="${p.name}"
                        class="prod-img w-full h-full object-cover"
                        onerror="this.closest('.overflow-hidden').innerHTML=posPlaceholder('${p.category_icon}')">
               </div>`
            : `<div class="h-28">${posPlaceholder(p.category_icon)}</div>`;

        card.innerHTML = `
            ${imgBlock}
            <!-- Category badge -->
            <span class="absolute top-2 left-2 text-[9px] font-bold px-2 py-0.5 rounded-full
                         bg-coffee-dark/75 text-white backdrop-blur-sm">
                ${p.category_icon} ${p.category_name}
            </span>
            <!-- Quick-add overlay -->
            <div class="absolute inset-0 flex items-center justify-center
                        opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none">
                <div class="w-11 h-11 rounded-full bg-accent/90 backdrop-blur-sm shadow-xl
                            flex items-center justify-center text-white text-xl font-bold
                            scale-75 group-hover:scale-100 transition-transform duration-250">+</div>
            </div>
            <!-- Info -->
            <div class="p-2.5 flex flex-col flex-1">
                <p class="text-[11px] font-bold text-coffee-dark leading-snug line-clamp-2 flex-1">${p.name}</p>
                <div class="flex items-center justify-between mt-2 pt-2 border-t border-cream-dark/60">
                    <span class="font-extrabold text-sm text-accent-dark font-heading">Bs.${parseFloat(p.price).toFixed(2)}</span>
                    <span class="w-6 h-6 rounded-lg bg-accent text-white text-xs font-bold
                                 flex items-center justify-center shadow-sm group-hover:bg-accent-dark transition-colors">+</span>
                </div>
            </div>
        `;

        grid.appendChild(card);
    });
}

/** Placeholder identical to admin products page */
function posPlaceholder(icon) {
    return `<div class="w-full h-full bg-gradient-to-br from-cream to-cream-dark flex flex-col
                        items-center justify-center text-coffee-medium/40 select-none">
                <div class="w-14 h-14 rounded-full bg-white/90 flex items-center justify-center shadow-md text-3xl mb-1">
                    ${icon}
                </div>
                <span class="text-[9px] uppercase font-extrabold tracking-widest text-coffee-medium/60 font-mono">Sin Imagen</span>
            </div>`;
}

// ═══════════════════════════════════════════════════════════
//  CART
// ═══════════════════════════════════════════════════════════

function posAddToCart(productId, ev, cardEl) {
    const product = POS_PRODUCTS.find(p => p.id === productId);
    if (!product) return;

    /* Ripple effect */
    if (ev && cardEl) {
        const rect = cardEl.getBoundingClientRect();
        const ripple = document.createElement('span');
        ripple.className = 'ripple-fx';
        ripple.style.left = (ev.clientX - rect.left - 30) + 'px';
        ripple.style.top  = (ev.clientY - rect.top  - 30) + 'px';
        cardEl.appendChild(ripple);
        setTimeout(() => ripple.remove(), 520);
    }

    /* Ring flash */
    if (cardEl) {
        cardEl.classList.add('ring-2', 'ring-accent', 'ring-offset-1');
        setTimeout(() => cardEl.classList.remove('ring-2', 'ring-accent', 'ring-offset-1'), 350);
    }

    const existing = posCart.find(i => i.id === productId);
    if (existing) {
        existing.quantity++;
    } else {
        posCart.push({
            id:       product.id,
            name:     product.name,
            price:    parseFloat(product.price),
            quantity: 1,
            icon:     product.category_icon,
        });
    }

    posRenderCart();

    /* Badge bounce */
    ['mob-fab-badge'].forEach(id => {
        const b = document.getElementById(id);
        if (b) { b.classList.add('scale-[1.6]'); setTimeout(() => b.classList.remove('scale-[1.6]'), 200); }
    });
}

function posUpdateQty(id, diff) {
    const item = posCart.find(i => i.id === id);
    if (!item) return;
    item.quantity += diff;
    if (item.quantity <= 0) posCart = posCart.filter(i => i.id !== id);
    posRenderCart();
}

function posRemove(id) {
    posCart = posCart.filter(i => i.id !== id);
    posRenderCart();
}

function posClearCart() {
    if (!posCart.length) return;
    posCart = [];
    posRenderCart();
}

function posRenderCart() {
    const targets = [
        { containerId: 'cart-items-desktop', subtotalId: 'cart-subtotal', totalId: 'cart-total' },
        { containerId: 'cart-items-mobile', subtotalId: 'cart-subtotal-mob', totalId: 'cart-total-mob' }
    ];

    const total = posCart.reduce((s, i) => s + i.price * i.quantity, 0);
    const fmt   = 'Bs. ' + total.toFixed(2);
    const count = posCart.reduce((s, i) => s + i.quantity, 0);

    targets.forEach(tgt => {
        const container = document.getElementById(tgt.containerId);
        const subtotalEl = document.getElementById(tgt.subtotalId);
        const totalEl = document.getElementById(tgt.totalId);

        if (subtotalEl) subtotalEl.textContent = fmt;
        if (totalEl) totalEl.textContent = fmt;

        if (!container) return;

        container.innerHTML = '';

        if (!posCart.length) {
            container.innerHTML = `
                <div class="h-full flex flex-col items-center justify-center gap-3 text-center py-12 px-4">
                    <div class="w-16 h-16 rounded-2xl bg-cream-dark flex items-center justify-center text-3xl shadow-inner">🛒</div>
                    <p class="font-bold text-coffee-medium/70 text-sm">Orden vacía</p>
                    <p class="text-xs text-coffee-light/70">Toca un producto para agregarlo al pedido.</p>
                </div>`;
            return;
        }

        posCart.forEach(item => {
            const row = document.createElement('div');
            row.className = 'cart-row flex items-center gap-2.5 bg-cream/50 hover:bg-cream border border-cream-dark rounded-xl p-2.5 group transition-colors';
            row.innerHTML = `
                <!-- Icon bubble -->
                <div class="shrink-0 w-9 h-9 rounded-xl bg-white border border-cream-dark shadow-sm
                            flex items-center justify-center text-lg">${item.icon}</div>
                <!-- Name & price/u -->
                <div class="flex-grow min-w-0">
                    <p class="text-[11px] font-bold text-coffee-dark truncate leading-snug">${item.name}</p>
                    <p class="text-[9px] text-coffee-light">Bs.${item.price.toFixed(2)} c/u</p>
                </div>
                <!-- Qty controls -->
                <div class="flex items-center gap-1.5 shrink-0">
                    <button onclick="posUpdateQty(${item.id},-1)"
                            class="w-6 h-6 rounded-lg bg-white border border-cream-dark text-xs font-bold text-coffee-medium
                                   hover:bg-red-50 hover:border-red-200 hover:text-red-500 transition-colors active:scale-90 flex items-center justify-center">−</button>
                    <span class="w-6 text-center text-xs font-extrabold text-coffee-dark">${item.quantity}</span>
                    <button onclick="posUpdateQty(${item.id},1)"
                            class="w-6 h-6 rounded-lg bg-white border border-cream-dark text-xs font-bold text-coffee-medium
                                   hover:bg-emerald-50 hover:border-emerald-200 hover:text-emerald-600 transition-colors active:scale-90 flex items-center justify-center">+</button>
                </div>
                <!-- Line total + remove -->
                <div class="shrink-0 text-right">
                    <p class="text-xs font-extrabold text-coffee-dark">Bs.${(item.price * item.quantity).toFixed(2)}</p>
                    <button onclick="posRemove(${item.id})"
                            class="text-[9px] text-red-400 hover:text-red-600 transition opacity-0 group-hover:opacity-100 font-medium">
                        quitar
                    </button>
                </div>
            `;
            container.appendChild(row);
        });
    });

    const badge = document.getElementById('mob-fab-badge');
    if (badge) badge.textContent = count;
}

// ═══════════════════════════════════════════════════════════
//  MOBILE BOTTOM SHEET
// ═══════════════════════════════════════════════════════════

function openMobCart() {
    mobCartOpen = true;
    const sheet = document.getElementById('mob-cart-sheet');
    const back  = document.getElementById('mob-cart-back');
    if (!sheet) return;
    back?.classList.remove('hidden');
    sheet.classList.remove('translate-y-full');
    sheet.classList.add('translate-y-0');
}

function closeMobCart() {
    mobCartOpen = false;
    const sheet = document.getElementById('mob-cart-sheet');
    const back  = document.getElementById('mob-cart-back');
    sheet?.classList.add('translate-y-full');
    sheet?.classList.remove('translate-y-0');
    back?.classList.add('hidden');
}

// ═══════════════════════════════════════════════════════════
//  CHECKOUT
// ═══════════════════════════════════════════════════════════

function openCheckout() {
    if (!posCart.length) { posToast('Agrega al menos un producto.', 'warn'); return; }
    const total = posCart.reduce((s, i) => s + i.price * i.quantity, 0);
    document.getElementById('ck-total').textContent  = 'Bs. ' + total.toFixed(2);
    document.getElementById('ck-pay').value          = '';
    document.getElementById('ck-change').textContent = 'Bs. 0.00';
    document.getElementById('checkout-modal').classList.remove('hidden');
}

function closeCheckout() {
    document.getElementById('checkout-modal').classList.add('hidden');
}

function ckSetBill(amount) {
    document.getElementById('ck-pay').value = amount;
    ckCalcChange();
}

function ckCalcChange() {
    const total  = posCart.reduce((s, i) => s + i.price * i.quantity, 0);
    const paid   = parseFloat(document.getElementById('ck-pay').value) || 0;
    const change = Math.max(0, paid - total);
    document.getElementById('ck-change').textContent = 'Bs. ' + change.toFixed(2);
}

function ckSubmit() {
    const total  = posCart.reduce((s, i) => s + i.price * i.quantity, 0);
    const paid   = parseFloat(document.getElementById('ck-pay').value) || 0;

    if (paid > 0 && paid < total) { posToast('El pago es menor al total.', 'warn'); return; }

    const btn = document.getElementById('ck-btn');
    btn.disabled = true;
    btn.innerHTML = `<svg class="spin w-4 h-4" viewBox="0 0 24 24" fill="none">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
    </svg> Procesando…`;

    fetch(POS_BASE + '/pos/checkout', {
        method:  'POST',
        headers: {'Content-Type': 'application/json'},
        body:    JSON.stringify({ items: posCart })
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Completar Venta`;
        if (data.success) {
            closeCheckout();
            posShowReceipt(data.sale_id, total, paid || total);
        } else {
            posToast('Error: ' + data.message, 'error');
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Completar Venta`;
        posToast('Error de conexión.', 'error');
    });
}

// ═══════════════════════════════════════════════════════════
//  RECEIPT
// ═══════════════════════════════════════════════════════════

function posShowReceipt(saleId, total, paid) {
    const now   = new Date();
    const dStr  = now.toLocaleDateString('es-BO') + ' ' + now.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
    const change = Math.max(0, paid - total);

    document.getElementById('tkt-date').textContent   = dStr;
    document.getElementById('tkt-id').textContent     = '#' + saleId;
    document.getElementById('tkt-total').textContent  = 'Bs. ' + total.toFixed(2);
    document.getElementById('tkt-paid').textContent   = 'Bs. ' + paid.toFixed(2);
    document.getElementById('tkt-change').textContent = 'Bs. ' + change.toFixed(2);

    const container = document.getElementById('tkt-items');
    container.innerHTML = '';
    posCart.forEach(item => {
        const row = document.createElement('div');
        row.className = 'flex justify-between';
        row.innerHTML = `<span class="truncate mr-2">${item.quantity}× ${item.name}</span>
                         <span class="shrink-0 font-semibold">Bs.${(item.price * item.quantity).toFixed(2)}</span>`;
        container.appendChild(row);
    });

    document.getElementById('receipt-modal').classList.remove('hidden');
}

function closeReceipt() {
    document.getElementById('receipt-modal').classList.add('hidden');
    posClearCart();
    if (POS_TRACK_RM) window.location.reload();
}

// ═══════════════════════════════════════════════════════════
//  TOAST NOTIFICATION
// ═══════════════════════════════════════════════════════════

function posToast(msg, type = 'info') {
    document.getElementById('pos-toast-el')?.remove();
    const colors = { warn: '#D97706', error: '#DC2626', info: '#3D1C02' };
    const icons  = { warn: '⚠️', error: '❌', info: 'ℹ️' };
    const t = document.createElement('div');
    t.id = 'pos-toast-el';
    t.style.cssText = `position:fixed;bottom:100px;left:50%;transform:translateX(-50%);
        z-index:999;background:${colors[type]||colors.info};color:#fff;
        padding:10px 20px;border-radius:14px;font-size:12px;font-weight:700;
        display:flex;align-items:center;gap:8px;box-shadow:0 8px 24px rgba(0,0,0,.25);
        transition:opacity .3s,transform .3s;white-space:nowrap;`;
    t.innerHTML = `<span>${icons[type]||icons.info}</span>${msg}`;
    document.body.appendChild(t);
    setTimeout(() => { t.style.opacity='0'; t.style.transform='translateX(-50%) translateY(8px)'; setTimeout(()=>t.remove(),350); }, 3000);
}
