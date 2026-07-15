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
         class="relative w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col"
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

            // Build items list – compact summary for card
            let itemsHtml = '';
            order.items.forEach(item => {
                itemsHtml += `
                    <div class="flex justify-between items-center py-3 border-b border-cream/50 last:border-0 text-lg">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">${item.product_icon || '🍔'}</span>
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
            itemsContainer.innerHTML += `
                <div class="flex items-center gap-2.5 p-2.5 rounded-xl ${idx % 2 === 0 ? 'bg-cream/60' : 'bg-white'} border border-cream-dark/50">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 border border-amber-200 flex items-center justify-center text-lg flex-shrink-0">
                        ${item.product_icon || '🍔'}
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
