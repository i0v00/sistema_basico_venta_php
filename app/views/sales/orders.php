<!-- Real-time active orders screen (cook & cashier interface) -->
<div id="orders-page-wrapper" class="space-y-6 animate-slide-up">
    <!-- Fullscreen Header (Hidden by default) -->
    <div id="fullscreen-header" class="hidden bg-coffee-dark text-white px-6 py-4 items-center justify-between shadow-lg border-b border-coffee-medium/30">
        <div class="flex items-center gap-3">
            <span class="text-3xl animate-pulse">🔴</span>
            <div>
                <h2 class="font-heading font-extrabold text-2xl uppercase tracking-wider text-accent">MONITOR EN VIVO</h2>
                <p class="text-xs text-cream-dark/60">Duke's Fast Food POS</p>
            </div>
        </div>
        <div class="flex items-center gap-6">
            <!-- Count selector -->
            <div class="flex items-center gap-3 bg-white/5 px-4 py-2 rounded-2xl border border-white/10">
                <span class="text-xs font-bold text-cream-dark/80 uppercase tracking-wider">Ver pedidos:</span>
                <div class="flex gap-1.5" id="fullscreen-count-buttons">
                    <button onclick="setFullscreenCount(1)" id="fbtn-1" class="w-9 h-9 flex items-center justify-center rounded-xl text-base font-black transition duration-150 hover:bg-white/15 text-white">1</button>
                    <button onclick="setFullscreenCount(2)" id="fbtn-2" class="w-9 h-9 flex items-center justify-center rounded-xl text-base font-black transition duration-150 hover:bg-white/15 text-white">2</button>
                    <button onclick="setFullscreenCount(3)" id="fbtn-3" class="w-9 h-9 flex items-center justify-center rounded-xl text-base font-black transition duration-150 hover:bg-white/15 text-white bg-accent shadow-md">3</button>
                    <button onclick="setFullscreenCount(4)" id="fbtn-4" class="w-9 h-9 flex items-center justify-center rounded-xl text-base font-black transition duration-150 hover:bg-white/15 text-white">4</button>
                    <button onclick="setFullscreenCount(5)" id="fbtn-5" class="w-9 h-9 flex items-center justify-center rounded-xl text-base font-black transition duration-150 hover:bg-white/15 text-white">5</button>
                </div>
            </div>
            <!-- Exit Fullscreen Button -->
            <button onclick="exitFullScreen()" class="bg-rose-600 hover:bg-rose-700 active:scale-95 text-white font-extrabold px-6 py-2.5 rounded-xl text-xs uppercase tracking-wider transition duration-150 shadow-md">
                Salir ✕
            </button>
        </div>
    </div>

    <!-- Normal Header with live indicator -->
    <div id="normal-header" class="bg-white p-6 rounded-2xl border border-cream-dark shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="font-heading font-extrabold text-3xl text-coffee-dark">Pedidos del Día</h1>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    En vivo
                </span>
            </div>
            <p class="text-sm text-coffee-light mt-1">Control de pedidos y entregas en tiempo real. Toca un pedido para ver el detalle.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3 self-start md:self-auto">
            <!-- Fullscreen trigger button -->
            <button onclick="enterFullScreen()" class="px-5 py-2 bg-coffee-dark hover:bg-coffee-medium text-white rounded-xl text-sm font-bold transition shadow-sm active:scale-95 flex items-center gap-1.5">
                📺 Pantalla Completa
            </button>

            <!-- Filters -->
            <div class="flex rounded-xl overflow-hidden border border-cream-dark shadow-sm bg-cream/10 p-1">
                <button id="filter-all" onclick="changeFilter('all')" class="px-4 py-2 text-xs font-bold rounded-lg transition-all duration-200 bg-coffee-dark text-white shadow-sm">
                    Todos
                </button>
                <button id="filter-pendiente" onclick="changeFilter('pendiente')" class="px-4 py-2 text-xs font-bold rounded-lg transition-all duration-200 text-coffee-medium hover:bg-cream-dark/55">
                    Pendientes 🟡
                </button>
                <button id="filter-entregado" onclick="changeFilter('entregado')" class="px-4 py-2 text-xs font-bold rounded-lg transition-all duration-200 text-coffee-medium hover:bg-cream-dark/55">
                    Entregados ✅
                </button>
                <button id="filter-finalizado" onclick="changeFilter('finalizado')" class="px-4 py-2 text-xs font-bold rounded-lg transition-all duration-200 text-coffee-medium hover:bg-cream-dark/55">
                    Finalizados 🔒
                </button>
            </div>
        </div>
    </div>

    <!-- Orders Grid -->
    <div id="orders-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Spinner placeholder -->
        <div class="col-span-full py-16 flex flex-col items-center justify-center text-coffee-light gap-3">
            <span class="animate-spin text-4xl">⏳</span>
            <p class="text-sm font-semibold">Cargando pedidos en tiempo real...</p>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════ -->
<!--  ORDER DETAIL MODAL                                 -->
<!-- ═══════════════════════════════════════════════════ -->
<div id="order-modal-backdrop"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 transition-all duration-300"
     style="display:none!important; background: rgba(61,28,2,0.55); backdrop-filter: blur(4px);"
     onclick="closeOrderModal(event)">

    <div id="order-modal"
         class="relative w-[95%] md:w-[25%] md:min-w-[320px] bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col"
         style="max-height: 88vh; transform: scale(0.95); opacity: 0; transition: transform 0.3s cubic-bezier(.4,0,.2,1), opacity 0.3s ease;">

        <!-- Modal Header (gradient) -->
        <div id="modal-header" class="relative px-5 py-4" style="background: linear-gradient(135deg, #3D1C02 0%, #7B4F2E 100%);">
            <!-- Close button -->
            <button onclick="closeOrderModal()" class="absolute top-3 right-3 w-7 h-7 bg-white/20 hover:bg-white/40 text-white rounded-full flex items-center justify-center text-sm font-bold transition-all active:scale-90">
                ✕
            </button>

            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center text-xl flex-shrink-0">
                    🧾
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white/60 text-[10px] font-bold uppercase tracking-widest">Detalle del Pedido</p>
                    <h2 id="modal-order-id" class="font-heading font-extrabold text-xl text-white leading-tight">#—</h2>
                    <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                        <span id="modal-time" class="text-white/75 text-[11px] font-semibold">—</span>
                        <span id="modal-status-badge" class="inline-block px-2 py-0.5 rounded-full text-[10px] font-extrabold border"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scrollable body -->
        <div class="flex-1 overflow-y-auto custom-scrollbar">

            <!-- Cashier + total strip -->
            <div class="px-5 py-2.5 border-b border-cream-dark flex items-center justify-between bg-cream/60">
                <div class="flex items-center gap-1.5 text-xs text-coffee-medium">
                    <span class="text-base">👤</span>
                    <span class="font-semibold">Cajero:</span>
                    <span id="modal-cashier" class="font-extrabold text-coffee-dark">—</span>
                </div>
                <div class="text-right">
                    <span class="text-[10px] text-coffee-light font-semibold block">Total</span>
                    <span id="modal-total" class="font-heading font-extrabold text-base text-coffee-dark">Bs. —</span>
                </div>
            </div>

            <!-- Items list -->
            <div class="px-5 pt-3 pb-2">
                <p class="text-[10px] font-bold text-coffee-light uppercase tracking-widest mb-2">Productos del pedido</p>
                <div id="modal-items" class="space-y-1.5">
                    <!-- Injected by JS -->
                </div>
            </div>

            <!-- Notes if any -->
            <div id="modal-notes-wrap" class="px-5 pb-3 hidden">
                <p class="text-[10px] font-bold text-coffee-light uppercase tracking-widest mb-1 mt-2">Notas</p>
                <p id="modal-notes" class="text-xs text-coffee-dark bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 font-medium"></p>
            </div>
        </div>

        <!-- Modal Footer Actions -->
        <div id="modal-actions" class="px-5 py-3.5 border-t border-cream-dark bg-cream/30 space-y-2">
            <!-- Injected by JS depending on status -->
        </div>
    </div>
</div>

<!-- Modal styles & Fullscreen layout styles -->
<style>
    #order-modal-backdrop.modal-visible {
        display: flex !important;
    }
    #order-modal-backdrop.modal-visible #order-modal {
        transform: scale(1) !important;
        opacity: 1 !important;
    }
    .order-card-clickable {
        cursor: pointer;
        transition: transform 0.18s ease, box-shadow 0.18s ease;
    }
    .order-card-clickable:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 32px rgba(61,28,2,0.13);
    }
    .order-card-clickable:active {
        transform: scale(0.98);
    }

    /* Fullscreen Mode Overrides */
    #orders-page-wrapper.fullscreen-active {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        background: #FFF8F0 !important;
        z-index: 9999 !important;
        padding: 0 !important;
        margin: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        overflow: hidden !important;
        max-width: 100vw !important;
    }

    #orders-page-wrapper.fullscreen-active #fullscreen-header {
        display: flex !important;
    }

    #orders-page-wrapper.fullscreen-active #normal-header {
        display: none !important;
    }

    #orders-page-wrapper.fullscreen-active #orders-container {
        flex-grow: 1 !important;
        padding: 24px !important;
        height: calc(100vh - 80px) !important;
        overflow-y: auto !important;
        align-items: stretch !important;
        display: grid !important;
        gap: 24px !important;
    }

    /* Grid layout configurations based on order counts in fullscreen */
    #orders-page-wrapper.fullscreen-active.fs-grid-1 #orders-container {
        grid-template-columns: 1fr !important;
    }
    #orders-page-wrapper.fullscreen-active.fs-grid-2 #orders-container {
        grid-template-columns: repeat(2, 1fr) !important;
    }
    #orders-page-wrapper.fullscreen-active.fs-grid-3 #orders-container {
        grid-template-columns: repeat(3, 1fr) !important;
    }
    #orders-page-wrapper.fullscreen-active.fs-grid-4 #orders-container {
        grid-template-columns: repeat(4, 1fr) !important;
    }
    #orders-page-wrapper.fullscreen-active.fs-grid-5 #orders-container {
        grid-template-columns: repeat(5, 1fr) !important;
    }

    /* Card customization in fullscreen */
    #orders-page-wrapper.fullscreen-active .order-card-clickable {
        height: 100% !important;
        max-height: 100% !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        border-width: 3px !important;
        box-shadow: 0 10px 25px -5px rgba(61,28,2,0.1) !important;
    }

    #orders-page-wrapper.fullscreen-active .order-card-clickable .flex-grow {
        max-height: none !important;
        overflow-y: auto !important;
    }

    /* Typography scaling for remote legibility */
    #orders-page-wrapper.fullscreen-active.fs-grid-1 .order-card-clickable { font-size: 1.8rem !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-1 .text-2xl { font-size: 3.8rem !important; line-height: 1 !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-1 .text-lg { font-size: 2.2rem !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-1 .text-xl { font-size: 2.8rem !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-1 .text-xs { font-size: 1.4rem !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-1 .text-sm { font-size: 1.6rem !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-1 .text-base { font-size: 2rem !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-1 .font-heading { font-size: 4rem !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-1 button { font-size: 1.8rem !important; padding: 22px !important; }

    #orders-page-wrapper.fullscreen-active.fs-grid-2 .order-card-clickable { font-size: 1.4rem !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-2 .text-2xl { font-size: 2.8rem !important; line-height: 1 !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-2 .text-lg { font-size: 1.8rem !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-2 .text-xl { font-size: 2.2rem !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-2 .text-xs { font-size: 1.1rem !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-2 .text-sm { font-size: 1.3rem !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-2 .text-base { font-size: 1.5rem !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-2 .font-heading { font-size: 3rem !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-2 button { font-size: 1.4rem !important; padding: 16px !important; }

    #orders-page-wrapper.fullscreen-active.fs-grid-3 .order-card-clickable { font-size: 1.1rem !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-3 .text-2xl { font-size: 2.2rem !important; line-height: 1 !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-3 .text-lg { font-size: 1.4rem !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-3 .text-xl { font-size: 1.8rem !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-3 .text-xs { font-size: 0.95rem !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-3 .text-sm { font-size: 1.05rem !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-3 .text-base { font-size: 1.15rem !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-3 .font-heading { font-size: 2.4rem !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-3 button { font-size: 1.1rem !important; padding: 12px !important; }

    #orders-page-wrapper.fullscreen-active.fs-grid-4 .order-card-clickable { font-size: 0.95rem !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-4 .text-2xl { font-size: 1.8rem !important; line-height: 1 !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-4 .text-lg { font-size: 1.2rem !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-4 .text-xl { font-size: 1.5rem !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-4 .font-heading { font-size: 1.9rem !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-4 button { font-size: 1rem !important; padding: 10px !important; }

    #orders-page-wrapper.fullscreen-active.fs-grid-5 .order-card-clickable { font-size: 0.85rem !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-5 .text-2xl { font-size: 1.5rem !important; line-height: 1 !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-5 .text-lg { font-size: 1.1rem !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-5 .text-xl { font-size: 1.3rem !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-5 .font-heading { font-size: 1.6rem !important; }
    #orders-page-wrapper.fullscreen-active.fs-grid-5 button { font-size: 0.9rem !important; padding: 8px !important; }
</style>

<script>
    const posCatIcons = {
        'hamburger': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9h18"/><path d="M3 15h18"/><path d="M5 6a7 7 0 0 1 14 0"/><rect x="3" y="9" width="18" height="6" rx="1"/><path d="M5 18h14a2 2 0 0 0 2-2v-1H3v1a2 2 0 0 0 2 2z"/></svg>',
        'fries': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 11v8a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-8"/><path d="M5 11l2-7h10l2 7"/><line x1="8" y1="4" x2="8" y2="11"/><line x1="12" y1="3" x2="12" y2="11"/><line x1="16" y1="4" x2="16" y2="11"/></svg>',
        'chicken': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/><path d="M12 6S7 8 5 13c-1.5 4 1 8 5 8s7-2 8-6c1-3.5-1-7-6-9z"/><path d="M9 15c1 1.5 3 2 5 1"/></svg>',
        'drink_cup': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 2h14l-1.5 16.5a2 2 0 0 1-2 1.5h-7a2 2 0 0 1-2-1.5L5 2z"/><line x1="9" y1="2" x2="9.5" y2="6"/><line x1="15" y1="2" x2="14.5" y2="6"/><path d="M3 2h18"/></svg>',
        'soda_bottle': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3h8v3l2 4v10a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V10l2-4V3z"/><line x1="8" y1="3" x2="8" y2="6"/><line x1="16" y1="3" x2="16" y2="6"/><line x1="6" y1="14" x2="18" y2="14"/></svg>',
        'package': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="16.5" y1="9.4" x2="7.5" y2="4.21"/><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>',
        'dessert': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 13 8 13s8-7.75 8-13a8 8 0 0 0-8-8z"/><circle cx="12" cy="10" r="3"/></svg>',
        'weekend': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
        'coffee': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V8z"/><line x1="6" y1="2" x2="6" y2="4"/><line x1="10" y1="2" x2="10" y2="4"/><line x1="14" y1="2" x2="14" y2="4"/></svg>',
        'pizza': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2C6.477 2 2 6.477 2 12l10 10L22 12C22 6.477 17.523 2 12 2z"/><path d="M12 2v10"/><circle cx="9" cy="9" r="1" fill="currentColor"/><circle cx="14" cy="13" r="1" fill="currentColor"/></svg>',
        'salad': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 14s3-3 6-3 6 3 9 3 6-3 6-3"/><path d="M2 20h20"/><path d="M8 14V8a4 4 0 0 1 8 0v6"/></svg>',
        'combo': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8"/><path d="M12 17v4"/><path d="M7 8h2"/><path d="M15 8h2"/><path d="M11 12h2"/></svg>'
    };

    let currentFilter = 'all';
    let previousOrdersCount = -1;
    let allOrders = []; // store locally for modal lookup
    let isFullscreenMode = false;
    let fullscreenCount = 3;

    function enterFullScreen() {
        const wrapper = document.getElementById('orders-page-wrapper');
        if (wrapper.requestFullscreen) {
            wrapper.requestFullscreen();
        } else if (wrapper.webkitRequestFullscreen) {
            wrapper.webkitRequestFullscreen();
        } else if (wrapper.msRequestFullscreen) {
            wrapper.msRequestFullscreen();
        }
    }

    function exitFullScreen() {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
    }

    function setFullscreenCount(count) {
        fullscreenCount = count;
        const wrapper = document.getElementById('orders-page-wrapper');
        // Remove all grid classes
        for (let i = 1; i <= 5; i++) {
            wrapper.classList.remove(`fs-grid-${i}`);
        }
        wrapper.classList.add(`fs-grid-${count}`);

        // Update active class on count buttons
        for (let i = 1; i <= 5; i++) {
            const btn = document.getElementById(`fbtn-${i}`);
            if (btn) {
                if (i === count) {
                    btn.className = "w-9 h-9 flex items-center justify-center rounded-xl text-base font-black transition duration-150 text-white bg-accent shadow-md";
                } else {
                    btn.className = "w-9 h-9 flex items-center justify-center rounded-xl text-base font-black transition duration-150 hover:bg-white/15 text-white";
                }
            }
        }

        renderOrders(allOrders);
    }

    // Monitor fullscreen change events
    document.addEventListener('fullscreenchange', handleFullscreenChange);
    document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
    document.addEventListener('mozfullscreenchange', handleFullscreenChange);
    document.addEventListener('MSFullscreenChange', handleFullscreenChange);

    function handleFullscreenChange() {
        const wrapper = document.getElementById('orders-page-wrapper');
        const isFS = document.fullscreenElement || document.webkitFullscreenElement || document.mozFullScreenElement || document.msFullscreenElement;
        
        if (isFS) {
            isFullscreenMode = true;
            wrapper.classList.add('fullscreen-active');
            setFullscreenCount(fullscreenCount);
        } else {
            isFullscreenMode = false;
            wrapper.classList.remove('fullscreen-active');
            for (let i = 1; i <= 5; i++) {
                wrapper.classList.remove(`fs-grid-${i}`);
            }
            renderOrders(allOrders);
        }
    }

    // Fetch and render orders from API
    async function loadOrders() {
        try {
            const response = await fetch(`${window.BASE_URL}/orders/json?status=${currentFilter}`);
            const data = await response.json();
            
            if (data.success) {
                allOrders = data.orders;
                renderOrders(data.orders);
            }
        } catch (error) {
            console.error('Error fetching orders:', error);
        }
    }

    // Render list of orders
    function renderOrders(orders) {
        const container = document.getElementById('orders-container');
        
        let displayOrders = orders;
        if (isFullscreenMode) {
            // Only show 'pendiente' status orders and slice
            displayOrders = orders.filter(o => o.status === 'pendiente').slice(0, fullscreenCount);
        }

        if (!displayOrders || displayOrders.length === 0) {
            container.innerHTML = `
                <div class="col-span-full bg-white p-12 text-center rounded-2xl border border-cream-dark shadow-sm text-coffee-light">
                    <span class="text-5xl block mb-3">🍽️</span>
                    <p class="font-bold text-lg">No hay pedidos registrados hoy con este filtro.</p>
                </div>
            `;
            previousOrdersCount = 0;
            return;
        }

        // Notification Sound or Visual Flash if new order arrives
        if (previousOrdersCount !== -1 && orders.length > previousOrdersCount) {
            playNewOrderNotification();
        }
        previousOrdersCount = orders.length;

        container.innerHTML = '';
        
        displayOrders.forEach(order => {
            const dateObj = new Date(order.sale_date);
            const timeStr = dateObj.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
            
            const isPend = order.status === 'pendiente';
            const isEntregado = order.status === 'entregado';
            const isFinalizado = order.status === 'finalizado';

            let statusClass = '';
            let statusLabel = '';
            let cardBorder = '';
            if (isPend) {
                statusClass = 'bg-amber-100 text-amber-800 border-amber-300';
                statusLabel = '🟡 PENDIENTE';
                cardBorder = 'border-amber-300 shadow-md ring-2 ring-amber-300/30';
            } else if (isEntregado) {
                statusClass = 'bg-emerald-100 text-emerald-800 border-emerald-300';
                statusLabel = '✅ ENTREGADO';
                cardBorder = 'border-emerald-300 shadow-sm';
            } else if (isFinalizado) {
                statusClass = 'bg-blue-100 text-blue-800 border-blue-300';
                statusLabel = '🔒 FINALIZADO';
                cardBorder = 'border-cream-dark shadow-sm opacity-70';
            }
            
            const orderCard = document.createElement('div');
            orderCard.className = `order-card-clickable bg-white rounded-2xl border ${cardBorder} overflow-hidden flex flex-col justify-between`;
            orderCard.id = `order-${order.id}`;
            orderCard.setAttribute('data-order-id', order.id);
            orderCard.addEventListener('click', (e) => {
                // Don't open modal if clicking action buttons inside the card footer
                if (e.target.closest('button') && !e.target.closest('[data-modal-btn]')) return;
                openOrderModal(order.id);
            });

            let itemsHtml = '';
            order.items.forEach(item => {
                let iconContent = posCatIcons[item.product_icon] ? posCatIcons[item.product_icon] : (item.product_icon || '🍔');
                // if it's text, it might just render without being an SVG, but we want it to look good. We will wrap in a smaller span if it's an SVG.
                let iconClass = posCatIcons[item.product_icon] ? 'w-6 h-6 inline-block' : 'text-2xl';
                itemsHtml += `
                    <div class="flex justify-between items-center py-3 border-b border-cream/50 last:border-0 text-lg">
                        <div class="flex items-center gap-3">
                            <span class="${iconClass}">${iconContent}</span>
                            <span class="font-extrabold text-coffee-dark">${item.quantity}unid.</span>
                            <span class="text-slate-800 font-bold">${item.product_name}</span>
                        </div>
                    </div>
                `;
            });

            // Action buttons (stop propagation so they don't open modal)
            let actionBtn = '';
            if (isPend) {
                actionBtn = `<button onclick="event.stopPropagation(); updateStatus(${order.id}, 'entregado')" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold py-3.5 px-4 rounded-xl text-base transition shadow-sm active:scale-95 flex items-center justify-center gap-1.5">
                       ✅ Listo / Entregar
                   </button>`;
            } else if (isEntregado) {
                actionBtn = `
                    <div class="flex flex-col sm:flex-row gap-2">
                        <button onclick="event.stopPropagation(); updateStatus(${order.id}, 'finalizado')" class="flex-1 bg-blue-700 hover:bg-blue-800 text-white font-extrabold py-3.5 px-2 rounded-xl text-base transition shadow-sm active:scale-95 flex items-center justify-center gap-1">
                            🔒 Finalizar
                        </button>
                        <button onclick="event.stopPropagation(); updateStatus(${order.id}, 'pendiente')" class="flex-1 bg-cream-dark hover:bg-cream-dark/80 text-coffee-dark font-bold py-3.5 px-2 rounded-xl text-base transition active:scale-95 flex items-center justify-center gap-1">
                            🔄 Regresar
                        </button>
                    </div>
                `;
            } else if (isFinalizado) {
                actionBtn = `<div class="text-center py-3 text-coffee-medium font-extrabold text-sm bg-coffee-dark/5 rounded-xl border border-cream-dark">
                    🔒 Pedido Finalizado
                </div>`;
            }

            // "Tap to view" hint chip
            const tapHint = `<div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                <span class="text-[9px] bg-coffee-dark/80 text-white px-2 py-0.5 rounded-full font-bold">Ver detalle</span>
            </div>`;

            orderCard.innerHTML = `
                <!-- Top Header -->
                <div class="p-4 bg-coffee-dark/5 border-b border-cream-dark flex justify-between items-center">
                    <div>
                        <span class="text-xs text-coffee-light font-bold">PEDIDO</span>
                        <div class="font-heading font-extrabold text-2xl text-coffee-dark">#${order.id}</div>
                    </div>
                    <div class="text-right">
                        <span class="text-sm text-coffee-light block font-semibold">${timeStr}</span>
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-extrabold border ${statusClass}">${statusLabel}</span>
                    </div>
                </div>

                <!-- Items list -->
                <div class="p-5 flex-grow custom-scrollbar overflow-y-auto max-h-60">
                    ${itemsHtml}
                </div>

                <!-- Tap hint -->
                <div class="px-4 py-1.5 bg-cream/30 border-t border-cream-dark">
                    <p class="text-[10px] text-coffee-light/70 font-semibold text-center tracking-wide">👆 Toca para ver detalle completo</p>
                </div>

                <!-- Footer details & Actions -->
                <div class="p-4 bg-cream/10 border-t border-cream-dark space-y-3">
                    <div class="flex justify-between items-center text-xs text-coffee-light">
                        <span>Cajero: <strong>${order.cashier_name || order.cashier_username || 'Sistema'}</strong></span>
                        <span class="text-base font-extrabold text-coffee-dark">Total: Bs. ${parseFloat(order.total).toFixed(2)}</span>
                    </div>
                    <div>
                        ${actionBtn}
                    </div>
                </div>
            `;

            container.appendChild(orderCard);
        });
    }

    // ─────────────────────────────────────────────
    //  MODAL LOGIC
    // ─────────────────────────────────────────────

    function openOrderModal(orderId) {
        const order = allOrders.find(o => o.id == orderId);
        if (!order) return;

        const backdrop = document.getElementById('order-modal-backdrop');
        const modal    = document.getElementById('order-modal');

        const dateObj = new Date(order.sale_date);
        const timeStr = dateObj.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
        const dateStr = dateObj.toLocaleDateString('es-ES', { weekday: 'short', day: '2-digit', month: 'short', year: 'numeric' });

        const isPend      = order.status === 'pendiente';
        const isEntregado = order.status === 'entregado';
        const isFinalizado = order.status === 'finalizado';

        // Status badge
        let statusClass = '', statusLabel = '';
        if (isPend)       { statusClass = 'bg-amber-100 text-amber-800 border-amber-300';   statusLabel = '🟡 Pendiente'; }
        else if (isEntregado) { statusClass = 'bg-emerald-100 text-emerald-800 border-emerald-300'; statusLabel = '✅ Entregado'; }
        else if (isFinalizado) { statusClass = 'bg-blue-100 text-blue-800 border-blue-300';       statusLabel = '🔒 Finalizado'; }

        // Populate fields
        document.getElementById('modal-order-id').textContent   = `#${order.id}`;
        document.getElementById('modal-time').textContent        = `${dateStr} · ${timeStr}`;
        document.getElementById('modal-cashier').textContent     = order.cashier_name || order.cashier_username || 'Sistema';
        document.getElementById('modal-total').textContent       = `Bs. ${parseFloat(order.total).toFixed(2)}`;

        const badge = document.getElementById('modal-status-badge');
        badge.className = `inline-block px-2.5 py-0.5 rounded-full text-[11px] font-extrabold border ${statusClass}`;
        badge.textContent = statusLabel;

        // Items
        const itemsContainer = document.getElementById('modal-items');
        itemsContainer.innerHTML = '';
        let subtotal = 0;
        order.items.forEach((item, idx) => {
            const lineTotal = parseFloat(item.unit_price || 0) * parseInt(item.quantity || 1);
            subtotal += lineTotal;
            const hasPrice = item.unit_price && parseFloat(item.unit_price) > 0;
            let iconContent = posCatIcons[item.product_icon] ? posCatIcons[item.product_icon] : (item.product_icon || '🍔');
            let iconClass = posCatIcons[item.product_icon] ? 'w-5 h-5 inline-block text-amber-800' : 'text-lg';
            itemsContainer.innerHTML += `
                <div class="flex items-center gap-2.5 p-2.5 rounded-xl ${idx % 2 === 0 ? 'bg-cream/60' : 'bg-white'} border border-cream-dark/50">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 border border-amber-200 flex items-center justify-center flex-shrink-0">
                        <span class="${iconClass}">${iconContent}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-coffee-dark text-xs leading-tight">${item.product_name}</p>
                        ${hasPrice ? `<p class="text-[11px] text-coffee-light font-semibold mt-0.5">Bs. ${parseFloat(item.unit_price).toFixed(2)} c/u</p>` : ''}
                    </div>
                    <div class="text-right flex-shrink-0">
                        <span class="inline-flex items-center justify-center bg-coffee-dark text-white text-[11px] font-extrabold rounded-md px-2 py-0.5 min-w-[2rem]">
                            ×${item.quantity}
                        </span>
                        ${hasPrice ? `<p class="text-[11px] font-bold text-coffee-medium mt-0.5">Bs. ${lineTotal.toFixed(2)}</p>` : ''}
                    </div>
                </div>
            `;
        });

        // Summary row
        itemsContainer.innerHTML += `
            <div class="mt-2.5 pt-2.5 border-t-2 border-coffee-dark/10 flex items-center justify-between">
                <div>
                    <p class="text-[11px] text-coffee-light font-semibold">${order.items.length} producto${order.items.length !== 1 ? 's' : ''} · ${order.items.reduce((a,b)=>a+parseInt(b.quantity||1),0)} uds.</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] text-coffee-light font-semibold">Total</p>
                    <p class="font-heading font-extrabold text-base text-coffee-dark">Bs. ${parseFloat(order.total).toFixed(2)}</p>
                </div>
            </div>
        `;

        // Notes
        const notesWrap = document.getElementById('modal-notes-wrap');
        if (order.notes && order.notes.trim()) {
            document.getElementById('modal-notes').textContent = order.notes;
            notesWrap.classList.remove('hidden');
        } else {
            notesWrap.classList.add('hidden');
        }

        // Actions footer
        const actionsEl = document.getElementById('modal-actions');
        let footerHtml = '';
        if (isPend) {
            footerHtml = `<button onclick="updateStatusFromModal(${order.id}, 'entregado')" class="w-full bg-emerald-700 hover:bg-emerald-800 active:scale-95 text-white font-extrabold py-3 rounded-xl text-sm transition shadow-md flex items-center justify-center gap-2">
                ✅ Marcar como Entregado
            </button>`;
        } else if (isEntregado) {
            footerHtml = `
                <button onclick="updateStatusFromModal(${order.id}, 'finalizado')" class="w-full bg-blue-700 hover:bg-blue-800 active:scale-95 text-white font-extrabold py-3 rounded-xl text-sm transition shadow-md flex items-center justify-center gap-2">
                    🔒 Finalizar Pedido
                </button>
                <button onclick="updateStatusFromModal(${order.id}, 'pendiente')" class="w-full bg-cream-dark hover:bg-cream-dark/80 active:scale-95 text-coffee-dark font-bold py-2.5 rounded-xl text-xs transition flex items-center justify-center gap-2">
                    🔄 Regresar a Pendiente
                </button>
            `;
        } else {
            footerHtml = `<div class="text-center py-2.5 text-coffee-medium font-bold text-xs bg-coffee-dark/5 rounded-xl border border-cream-dark">🔒 Pedido Finalizado — Sin acciones disponibles</div>`;
        }
        actionsEl.innerHTML = footerHtml;

        // Show modal with animation
        backdrop.style.removeProperty('display');
        backdrop.classList.add('modal-visible');
        // Lock body scroll
        document.body.style.overflow = 'hidden';
    }

    function closeOrderModal(event) {
        if (event && event.target !== document.getElementById('order-modal-backdrop')) return;
        const backdrop = document.getElementById('order-modal-backdrop');
        const modal    = document.getElementById('order-modal');
        backdrop.classList.remove('modal-visible');
        document.body.style.overflow = '';
        setTimeout(() => {
            backdrop.style.display = 'none';
        }, 300);
    }

    // Close modal button (explicit, not backdrop click)
    document.addEventListener('DOMContentLoaded', () => {
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                const backdrop = document.getElementById('order-modal-backdrop');
                if (backdrop.classList.contains('modal-visible')) {
                    backdrop.classList.remove('modal-visible');
                    document.body.style.overflow = '';
                    setTimeout(() => { backdrop.style.display = 'none'; }, 300);
                }
            }
        });
    });

    // Update status from inside modal
    async function updateStatusFromModal(saleId, newStatus) {
        const actionsEl = document.getElementById('modal-actions');
        actionsEl.innerHTML = `<div class="text-center py-3 text-coffee-medium font-semibold text-sm">Actualizando...</div>`;
        await updateStatus(saleId, newStatus);
        // Re-open updated order
        setTimeout(() => openOrderModal(saleId), 400);
    }

    // ─────────────────────────────────────────────
    //  STATUS UPDATE API
    // ─────────────────────────────────────────────

    async function updateStatus(saleId, newStatus) {
        try {
            const card = document.getElementById(`order-${saleId}`);
            if (card) card.style.opacity = '0.5';

            const response = await fetch(`${window.BASE_URL}/orders/status`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ sale_id: saleId, status: newStatus })
            });
            const data = await response.json();
            
            if (data.success) {
                loadOrders();
            } else {
                alert('No se pudo actualizar el estado del pedido.');
                loadOrders();
            }
        } catch (error) {
            console.error('Error updating order:', error);
            loadOrders();
        }
    }

    // Update filter views
    function changeFilter(filter) {
        currentFilter = filter;
        previousOrdersCount = -1; // Reset flash checking
        
        // Update filter tabs UI
        ['all', 'pendiente', 'entregado', 'finalizado'].forEach(f => {
            const btn = document.getElementById(`filter-${f}`);
            if (f === filter) {
                btn.className = "px-4 py-2 text-xs font-bold rounded-lg transition-all duration-200 bg-coffee-dark text-white shadow-sm";
            } else {
                btn.className = "px-4 py-2 text-xs font-bold rounded-lg transition-all duration-200 text-coffee-medium hover:bg-cream-dark/55";
            }
        });

        loadOrders();
    }

    // Flash screen on new order
    function playNewOrderNotification() {
        const body = document.body;
        body.classList.add('bg-accent/10');
        setTimeout(() => {
            body.classList.remove('bg-accent/10');
        }, 800);
    }

    // Initialize polling
    loadOrders();
    setInterval(loadOrders, 3000); // 3 seconds real-time update
</script>
