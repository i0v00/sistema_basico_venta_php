<?php
$trackRawMaterials = (getSetting('track_raw_materials', '0') === '1') ? 1 : 0;

// ── Professional SVG icons map (matches categories/index.php) ──────────
$posCatIcons = [
    'hamburger'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9h18"/><path d="M3 15h18"/><path d="M5 6a7 7 0 0 1 14 0"/><rect x="3" y="9" width="18" height="6" rx="1"/><path d="M5 18h14a2 2 0 0 0 2-2v-1H3v1a2 2 0 0 0 2 2z"/></svg>',
    'fries'       => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 11v8a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-8"/><path d="M5 11l2-7h10l2 7"/><line x1="8" y1="4" x2="8" y2="11"/><line x1="12" y1="3" x2="12" y2="11"/><line x1="16" y1="4" x2="16" y2="11"/></svg>',
    'chicken'     => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/><path d="M12 6S7 8 5 13c-1.5 4 1 8 5 8s7-2 8-6c1-3.5-1-7-6-9z"/><path d="M9 15c1 1.5 3 2 5 1"/></svg>',
    'drink_cup'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 2h14l-1.5 16.5a2 2 0 0 1-2 1.5h-7a2 2 0 0 1-2-1.5L5 2z"/><line x1="9" y1="2" x2="9.5" y2="6"/><line x1="15" y1="2" x2="14.5" y2="6"/><path d="M3 2h18"/></svg>',
    'soda_bottle' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3h8v3l2 4v10a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V10l2-4V3z"/><line x1="8" y1="3" x2="8" y2="6"/><line x1="16" y1="3" x2="16" y2="6"/><line x1="6" y1="14" x2="18" y2="14"/></svg>',
    'package'     => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="16.5" y1="9.4" x2="7.5" y2="4.21"/><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>',
    'dessert'     => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 13 8 13s8-7.75 8-13a8 8 0 0 0-8-8z"/><circle cx="12" cy="10" r="3"/></svg>',
    'weekend'     => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
    'coffee'      => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V8z"/><line x1="6" y1="2" x2="6" y2="4"/><line x1="10" y1="2" x2="10" y2="4"/><line x1="14" y1="2" x2="14" y2="4"/></svg>',
    'pizza'       => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2C6.477 2 2 6.477 2 12l10 10L22 12C22 6.477 17.523 2 12 2z"/><path d="M12 2v10"/><circle cx="9" cy="9" r="1" fill="currentColor"/><circle cx="14" cy="13" r="1" fill="currentColor"/></svg>',
    'salad'       => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 14s3-3 6-3 6 3 9 3 6-3 6-3"/><path d="M2 20h20"/><path d="M8 14V8a4 4 0 0 1 8 0v6"/></svg>',
    'combo'       => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8"/><path d="M12 17v4"/><path d="M7 8h2"/><path d="M15 8h2"/><path d="M11 12h2"/></svg>',
];
?>
<!– ══════════════════════════════════════════════════
    POS — Full-screen kiosk layout
    Layout: [Category Rail | Product Grid] [Cart Panel]
══════════════════════════════════════════════════ –>

<style>
    /* Hide the main footer on POS for full-screen feel */
    body > footer { display: none !important; }
    /* Hide main padding wrapper on POS */
    body > main { padding: 0 !important; }

    /* Category rail active pill */
    .cat-pill.active {
        background: #E07B39;
        color: #fff;
        border-color: #E07B39;
        box-shadow: 0 4px 12px rgba(224,123,57,.35);
    }

    /* Product card img zoom on hover */
    .prod-img { transition: transform .45s cubic-bezier(.16,1,.3,1); }
    .prod-card:hover .prod-img { transform: scale(1.08); }

    /* Cart item row */
    @keyframes rowIn {
        from { opacity:0; transform: translateX(12px); }
        to   { opacity:1; transform: translateX(0); }
    }
    .cart-row { animation: rowIn .22s ease both; }

    /* Ripple on product tap */
    @keyframes ripple {
        from { transform: scale(0); opacity: .45; }
        to   { transform: scale(3); opacity: 0; }
    }
    .ripple-fx {
        position:absolute; border-radius:50%;
        background: rgba(224,123,57,.55);
        width: 60px; height: 60px;
        pointer-events:none;
        animation: ripple .5s linear forwards;
    }

    /* Checkout modal slide-up */
    @keyframes slideUp {
        from { opacity:0; transform: translateY(32px) scale(.97); }
        to   { opacity:1; transform: translateY(0) scale(1); }
    }
    .modal-panel { animation: slideUp .32s cubic-bezier(.16,1,.3,1) both; }

    /* Receipt pop */
    @keyframes popIn {
        0%  { opacity:0; transform: scale(.8); }
        70% { transform: scale(1.03); }
        100%{ opacity:1; transform: scale(1); }
    }
    .receipt-panel { animation: popIn .38s cubic-bezier(.16,1,.3,1) both; }

    /* Spinner */
    @keyframes spin { to { transform: rotate(360deg); } }
    .spin { animation: spin .7s linear infinite; }
</style>

<?php
// Detect screen context — PHP can only know route, JS will handle UI
?>

<!-- ── Root wrapper: full viewport minus the top navbar ────────────── -->
<div id="pos-root"
     class="flex h-[calc(100vh-4rem)] bg-cream overflow-hidden">

    <!-- ══════════════════════════════════════════════════════════
         LEFT ZONE — Category sidebar + Product grid
    ══════════════════════════════════════════════════════════ -->
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

        <!-- ── Top bar: Search + Category pills ─────────────── -->
        <div class="shrink-0 bg-white border-b border-cream-dark px-4 py-3 space-y-3">

            <!-- Search -->
            <div class="relative">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-coffee-light pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input id="pos-search"
                       type="text" oninput="posFilter()"
                       placeholder="Buscar producto o código…"
                       class="w-full pl-10 pr-4 py-2.5 bg-cream rounded-xl border border-cream-dark text-sm
                              text-coffee-dark placeholder-coffee-light/60 focus:outline-none focus:ring-2
                              focus:ring-accent/50 focus:border-accent transition">
            </div>

            <!-- Category pills (horizontal scroll) -->
            <div class="flex gap-2 overflow-x-auto pb-0.5 custom-scrollbar snap-x">
                <button id="cat-all"
                        onclick="posSelectCat(null)"
                        class="cat-pill active snap-start shrink-0 flex items-center gap-1.5
                               px-4 py-2 rounded-full text-xs font-bold border border-transparent
                               transition-all duration-200 whitespace-nowrap">
                    🍽️ Todos
                </button>
                <?php foreach ($categories as $cat):
                    $iconKey = $cat['icon'] ?? '';
                    $hasSvg  = isset($posCatIcons[$iconKey]);
                ?>
                <button id="cat-<?= $cat['id'] ?>"
                        onclick="posSelectCat(<?= $cat['id'] ?>)"
                        class="cat-pill snap-start shrink-0 flex items-center gap-1.5
                               px-4 py-2 rounded-full text-xs font-bold bg-white border border-cream-dark
                               text-coffee-dark hover:border-accent/60 hover:text-accent
                               transition-all duration-200 whitespace-nowrap">
                    <?php if ($hasSvg): ?>
                    <span class="w-3.5 h-3.5 flex-shrink-0 [&_svg]:w-3.5 [&_svg]:h-3.5"><?= $posCatIcons[$iconKey] ?></span>
                    <?php else: ?>
                    <span><?= e($iconKey) ?></span>
                    <?php endif; ?>
                    <?= e($cat['name']) ?>
                </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ── Product grid ──────────────────────────────────── -->
        <div class="flex-1 overflow-y-auto custom-scrollbar p-4">

            <!-- Grid -->
            <div id="pos-grid"
                 class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
            </div>

            <!-- Empty state -->
            <div id="pos-empty"
                 class="hidden flex-col items-center justify-center h-full py-24 text-center gap-3">
                <div class="w-20 h-20 rounded-2xl bg-cream-dark flex items-center justify-center text-4xl">🍟</div>
                <p class="font-bold text-coffee-medium">Sin resultados</p>
                <p class="text-xs text-coffee-light">Intenta buscar con otro término o categoría.</p>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════
         RIGHT ZONE — Order cart
         Desktop: visible sidebar
         Mobile: fixed bottom sheet (slide-up)
    ══════════════════════════════════════════════════════════ -->
    <!-- Desktop sidebar -->
    <div id="cart-desktop"
         class="hidden lg:flex flex-col w-[340px] xl:w-[380px] shrink-0
                bg-white border-l border-cream-dark overflow-hidden">
        <!-- Cart header -->
        <div class="shrink-0 px-5 py-4 border-b border-cream-dark flex items-center justify-between bg-coffee-dark text-white">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="font-heading font-extrabold text-sm tracking-wide">Orden de Compra</span>
            </div>
        </div>
        <!-- Cart items -->
        <div id="cart-items-desktop" class="flex-1 overflow-y-auto custom-scrollbar px-4 py-3 space-y-2"></div>
        <!-- Totals & actions -->
        <div class="shrink-0 border-t border-cream-dark bg-cream/40 px-4 py-4 space-y-3">
            <div class="space-y-1.5 text-sm">
                <div class="flex justify-between text-coffee-light">
                    <span>Subtotal</span>
                    <span id="cart-subtotal" class="font-semibold text-coffee-dark">Bs. 0.00</span>
                </div>
                <div class="flex justify-between font-extrabold text-base border-t border-cream-dark pt-2 mt-1">
                    <span class="text-coffee-dark">Total</span>
                    <span id="cart-total" class="text-accent-dark font-heading text-lg">Bs. 0.00</span>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2.5">
                <button onclick="posClearCart()"
                        class="flex items-center justify-center gap-1.5 py-3 rounded-xl border border-cream-dark bg-white hover:bg-cream text-coffee-medium text-xs font-bold transition-all active:scale-95">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Vaciar
                </button>
                <button onclick="openCheckout()"
                        class="flex items-center justify-center gap-1.5 py-3 rounded-xl bg-accent hover:bg-accent-dark text-white text-xs font-extrabold transition-all shadow-md shadow-accent/20 active:scale-95">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V5a2 2 0 00-2-2H4a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Cobrar
                </button>
            </div>
        </div>
    </div>

    <!-- ────────────────────────────────────────────────────── -->
    <!-- Mobile: floating FAB + bottom sheet -->
    <!-- ────────────────────────────────────────────────────── -->

    <!-- FAB trigger (only on mobile) -->
    <button id="mob-cart-fab"
            onclick="openMobCart()"
            class="lg:hidden fixed bottom-5 right-5 z-50
                   flex items-center gap-2 bg-accent hover:bg-accent-dark text-white
                   font-bold px-5 py-3.5 rounded-full shadow-2xl shadow-accent/30
                   transition-all duration-200 active:scale-95">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <span>Ver Orden</span>
        <span id="mob-fab-badge"
              class="bg-white text-accent font-heading font-extrabold text-[11px]
                     w-5 h-5 rounded-full flex items-center justify-center">0</span>
    </button>

    <!-- Mobile backdrop -->
    <div id="mob-cart-back"
         onclick="closeMobCart()"
         class="lg:hidden hidden fixed inset-0 z-[60] bg-black/40 backdrop-blur-sm">
    </div>

    <div id="mob-cart-sheet"
         class="lg:hidden fixed bottom-0 left-0 right-0 z-[61]
                h-[85dvh] bg-white rounded-t-3xl shadow-2xl flex flex-col
                translate-y-full transition-transform duration-400 ease-[cubic-bezier(.32,0,.67,0)]">
        <!-- Drag handle -->
        <div class="flex justify-center pt-3 pb-1 shrink-0">
            <div class="w-10 h-1 rounded-full bg-cream-dark"></div>
        </div>
        <!-- Cart header -->
        <div class="shrink-0 px-5 py-3.5 border-b border-cream-dark flex items-center justify-between bg-coffee-dark text-white">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="font-heading font-extrabold text-sm tracking-wide">Orden de Compra</span>
            </div>
            <button onclick="closeMobCart()"
                    class="w-7 h-7 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <!-- Cart items -->
        <div id="cart-items-mobile" class="flex-1 overflow-y-auto custom-scrollbar px-4 py-3 space-y-2"></div>
        <!-- Totals & actions -->
        <div class="shrink-0 border-t border-cream-dark bg-cream/40 px-4 py-4 space-y-3">
            <div class="space-y-1.5 text-sm">
                <div class="flex justify-between text-coffee-light">
                    <span>Subtotal</span>
                    <span id="cart-subtotal-mob" class="font-semibold text-coffee-dark">Bs. 0.00</span>
                </div>
                <div class="flex justify-between font-extrabold text-base border-t border-cream-dark pt-2 mt-1">
                    <span class="text-coffee-dark">Total</span>
                    <span id="cart-total-mob" class="text-accent-dark font-heading text-lg">Bs. 0.00</span>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2.5">
                <button onclick="posClearCart()"
                        class="flex items-center justify-center gap-1.5 py-3 rounded-xl border border-cream-dark bg-white hover:bg-cream text-coffee-medium text-xs font-bold transition-all active:scale-95">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Vaciar
                </button>
                <button onclick="openCheckout()"
                        class="flex items-center justify-center gap-1.5 py-3 rounded-xl bg-accent hover:bg-accent-dark text-white text-xs font-extrabold transition-all shadow-md shadow-accent/20 active:scale-95">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V5a2 2 0 00-2-2H4a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Cobrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════
     CHECKOUT MODAL
══════════════════════════════════════════════════════════ -->
<div id="checkout-modal"
     class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-coffee-dark/60 backdrop-blur-sm" onclick="closeCheckout()"></div>

    <div class="modal-panel relative z-10 w-[22%] min-w-[300px] max-w-[340px] bg-white rounded-xl shadow-2xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-coffee-dark to-coffee-medium text-white px-4 py-3 flex items-center justify-between">
            <div>
                <h4 class="font-heading font-extrabold text-sm">Confirmar Cobro</h4>
            </div>
            <button onclick="closeCheckout()"
                    class="w-6 h-6 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="p-4 space-y-3">
            <!-- Total bubble -->
            <div class="rounded-xl bg-gradient-to-br from-amber-50 to-orange-50 border border-accent/20 p-3 text-center">
                <span class="text-[9px] font-bold uppercase tracking-widest text-accent-dark block mb-0.5">Total a Cobrar</span>
                <span id="ck-total" class="text-2xl font-heading font-extrabold text-coffee-dark">Bs. 0.00</span>
            </div>

            <!-- Payment Method Selector (REQUIRED) -->
            <div>
                <label class="text-[10px] font-bold text-coffee-dark block mb-1.5 uppercase tracking-wider">
                    Tipo de Pago <span class="text-rose-600">*</span>
                </label>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" id="pm-btn-efectivo" onclick="setPaymentMethod('efectivo')"
                            class="pm-btn flex items-center justify-center gap-1.5 py-2 px-2 rounded-lg font-bold text-xs border-2 border-cream-dark bg-cream hover:bg-cream-dark text-coffee-dark transition-all">
                        <span>💵</span> Efectivo
                    </button>
                    <button type="button" id="pm-btn-qr" onclick="setPaymentMethod('qr')"
                            class="pm-btn flex items-center justify-center gap-1.5 py-2 px-2 rounded-lg font-bold text-xs border-2 border-cream-dark bg-cream hover:bg-cream-dark text-coffee-dark transition-all">
                        <span>📱</span> QR
                    </button>
                </div>
            </div>

            <!-- Cash Payment Details (Visible when Efectivo selected or default) -->
            <div id="cash-details-box" class="space-y-2">
                <div>
                    <label class="text-[10px] font-bold text-coffee-dark block mb-1">Recibido:</label>
                    <div class="relative">
                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 font-bold text-xs text-coffee-medium">Bs.</span>
                        <input id="ck-pay" type="number" oninput="ckCalcChange()" step="0.01" min="0" placeholder="0.00"
                               class="w-full pl-8 pr-3 py-2 rounded-lg border-2 border-cream-dark focus:outline-none focus:border-accent font-bold text-lg text-coffee-dark bg-cream/40 transition">
                    </div>
                </div>

                <!-- Quick bills -->
                <div>
                    <div class="grid grid-cols-6 gap-1">
                        <?php foreach ([5, 10, 20, 50, 100, 200] as $b): ?>
                        <button onclick="ckSetBill(<?= $b ?>)"
                                class="py-1 rounded-md text-[9px] font-bold bg-cream hover:bg-cream-dark border border-cream-dark hover:border-accent hover:text-accent text-coffee-dark transition-all active:scale-95">
                            <?= $b ?>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Change -->
                <div class="flex justify-between items-center rounded-lg bg-emerald-50 border border-emerald-100 px-3 py-2">
                    <span class="text-[10px] font-semibold text-emerald-900">Cambio:</span>
                    <span id="ck-change" class="text-sm font-extrabold text-emerald-700 font-heading">Bs. 0.00</span>
                </div>
            </div>

            <!-- Submit -->
            <button id="ck-btn" onclick="ckSubmit()"
                    class="w-full bg-accent hover:bg-accent-dark active:scale-95 text-white font-heading font-bold py-2.5 rounded-lg shadow-md shadow-accent/25 transition-all text-xs flex items-center justify-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Completar Venta
            </button>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════════════
     TICKET / RECEIPT MODAL
══════════════════════════════════════════════════════════ -->
<div id="receipt-modal"
     class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-coffee-dark/70 backdrop-blur-sm" onclick="closeReceipt()"></div>
    <div class="receipt-panel relative z-10 w-full max-w-xs bg-white rounded-2xl shadow-2xl overflow-hidden">

        <!-- Top gradient header -->
        <div class="bg-gradient-to-br from-accent to-accent-dark p-5 text-center text-white">
            <div class="w-14 h-14 mx-auto rounded-full bg-white/20 border border-white/30
                        flex items-center justify-center text-3xl mb-2 shadow-lg">🧾</div>
            <h4 class="font-heading font-extrabold text-lg">¡Venta Registrada!</h4>
            <p class="text-white/70 text-xs mt-1">Gracias por tu preferencia</p>
        </div>

        <!-- Zigzag divider top -->
        <div class="h-3" style="background: repeating-linear-gradient(90deg,#fff 0,#fff 8px,#E07B39 8px,#E07B39 16px);"></div>

        <!-- Body -->
        <div class="px-5 py-4 space-y-1.5 font-mono text-xs text-coffee-dark">
            <div class="flex justify-between text-coffee-light">
                <span>Fecha:</span><span id="tkt-date" class="font-semibold text-coffee-dark"></span>
            </div>
            <div class="flex justify-between text-coffee-light">
                <span>Ticket N°:</span><span id="tkt-id" class="font-extrabold text-accent"></span>
            </div>
            <div class="flex justify-between text-coffee-light">
                <span>Pago con:</span><span id="tkt-pm" class="font-extrabold text-coffee-dark"></span>
            </div>
            <div class="border-t border-dashed border-coffee-medium/20 my-2"></div>
            <div id="tkt-items" class="space-y-1"></div>
            <div class="border-t border-dashed border-coffee-medium/20 my-2"></div>
            <div class="flex justify-between font-bold text-sm">
                <span>TOTAL</span><span id="tkt-total"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-coffee-light">Recibido</span><span id="tkt-paid"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-coffee-light">Cambio</span>
                <span id="tkt-change" class="font-bold text-emerald-600"></span>
            </div>
        </div>

        <!-- Zigzag divider bottom -->
        <div class="h-3" style="background: repeating-linear-gradient(90deg,#fff 0,#fff 8px,#F5E6D3 8px,#F5E6D3 16px);"></div>

        <div class="px-5 pb-5 pt-2">
            <button onclick="closeReceipt()"
                    class="w-full bg-coffee-dark hover:bg-coffee-medium text-white font-heading font-bold
                           py-3 rounded-xl transition-all active:scale-95 text-sm">
                Nueva Venta 🍔
            </button>
        </div>
    </div>
</div>

<script>
    const POS_PRODUCTS  = <?= json_encode(array_values($products)) ?>;
    const POS_BASE      = '<?= BASE_URL ?>';
    const POS_TRACK_RM  = <?= $trackRawMaterials ?>;
    const POS_ICONS     = <?= json_encode($posCatIcons) ?>;
</script>
<script src="<?= BASE_URL ?>/assets/app.js?v=<?= time() ?>"></script>
