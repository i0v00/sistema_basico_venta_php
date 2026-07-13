<!-- Real-time active orders screen (cook & cashier interface) -->
<div class="space-y-6 animate-slide-up">
    <!-- Header with live indicator -->
    <div class="bg-white p-6 rounded-2xl border border-cream-dark shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
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
        
        <!-- Filters -->
        <div class="flex rounded-xl overflow-hidden border border-cream-dark shadow-sm bg-cream/10 p-1 self-start md:self-auto">
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
         class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col"
         style="max-height: 90vh; transform: scale(0.95); opacity: 0; transition: transform 0.3s cubic-bezier(.4,0,.2,1), opacity 0.3s ease;">

        <!-- Modal Header (gradient) -->
        <div id="modal-header" class="relative p-6 pb-5" style="background: linear-gradient(135deg, #3D1C02 0%, #7B4F2E 100%);">
            <!-- Close button -->
            <button onclick="closeOrderModal()" class="absolute top-4 right-4 w-8 h-8 bg-white/20 hover:bg-white/40 text-white rounded-full flex items-center justify-center text-lg font-bold transition-all active:scale-90">
                ✕
            </button>

            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/15 flex items-center justify-center text-3xl">
                    🧾
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-white/70 text-xs font-bold uppercase tracking-widest">Detalle del Pedido</p>
                    <h2 id="modal-order-id" class="font-heading font-extrabold text-3xl text-white leading-tight">#—</h2>
                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                        <span id="modal-time" class="text-white/80 text-xs font-semibold">—</span>
                        <span id="modal-status-badge" class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-extrabold border"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scrollable body -->
        <div class="flex-1 overflow-y-auto custom-scrollbar">

            <!-- Cashier + total strip -->
            <div class="px-6 py-3 border-b border-cream-dark flex items-center justify-between bg-cream/60">
                <div class="flex items-center gap-2 text-sm text-coffee-medium">
                    <span class="text-lg">👤</span>
                    <span class="font-semibold">Cajero:</span>
                    <span id="modal-cashier" class="font-extrabold text-coffee-dark">—</span>
                </div>
                <div class="text-right">
                    <span class="text-xs text-coffee-light font-semibold block">Total</span>
                    <span id="modal-total" class="font-heading font-extrabold text-xl text-coffee-dark">Bs. —</span>
                </div>
            </div>

            <!-- Items list -->
            <div class="px-6 pt-4 pb-2">
                <p class="text-[11px] font-bold text-coffee-light uppercase tracking-widest mb-3">Productos del pedido</p>
                <div id="modal-items" class="space-y-2">
                    <!-- Injected by JS -->
                </div>
            </div>

            <!-- Notes if any -->
            <div id="modal-notes-wrap" class="px-6 pb-4 hidden">
                <p class="text-[11px] font-bold text-coffee-light uppercase tracking-widest mb-1 mt-3">Notas</p>
                <p id="modal-notes" class="text-sm text-coffee-dark bg-amber-50 border border-amber-200 rounded-xl px-4 py-2.5 font-medium"></p>
            </div>
        </div>

        <!-- Modal Footer Actions -->
        <div id="modal-actions" class="p-5 border-t border-cream-dark bg-cream/30 space-y-2.5">
            <!-- Injected by JS depending on status -->
        </div>
    </div>
</div>

<!-- Modal styles -->
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
</style>

<script>
    let currentFilter = 'all';
    let previousOrdersCount = -1;
    let allOrders = []; // store locally for modal lookup

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
        
        if (!orders || orders.length === 0) {
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
        
        orders.forEach(order => {
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
                <div class="flex items-center gap-3 p-3 rounded-xl ${idx % 2 === 0 ? 'bg-cream/60' : 'bg-white'} border border-cream-dark/50">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-2xl flex-shrink-0">
                        ${item.product_icon || '🍔'}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-coffee-dark text-sm truncate">${item.product_name}</p>
                        ${hasPrice ? `<p class="text-xs text-coffee-light font-semibold">Bs. ${parseFloat(item.unit_price).toFixed(2)} c/u</p>` : ''}
                    </div>
                    <div class="text-right flex-shrink-0">
                        <span class="inline-flex items-center justify-center bg-coffee-dark text-white text-xs font-extrabold rounded-lg px-2.5 py-1 min-w-[2.5rem]">
                            ×${item.quantity}
                        </span>
                        ${hasPrice ? `<p class="text-xs font-bold text-coffee-medium mt-1">Bs. ${lineTotal.toFixed(2)}</p>` : ''}
                    </div>
                </div>
            `;
        });

        // Summary row
        itemsContainer.innerHTML += `
            <div class="mt-3 pt-3 border-t-2 border-coffee-dark/10 flex items-center justify-between">
                <div>
                    <p class="text-xs text-coffee-light font-semibold">${order.items.length} producto${order.items.length !== 1 ? 's' : ''} · ${order.items.reduce((a,b)=>a+parseInt(b.quantity||1),0)} unidades</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-coffee-light font-semibold">Total del pedido</p>
                    <p class="font-heading font-extrabold text-xl text-coffee-dark">Bs. ${parseFloat(order.total).toFixed(2)}</p>
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
            footerHtml = `<button onclick="updateStatusFromModal(${order.id}, 'entregado')" class="w-full bg-emerald-700 hover:bg-emerald-800 active:scale-95 text-white font-extrabold py-3.5 rounded-xl text-base transition shadow-md flex items-center justify-center gap-2">
                ✅ Marcar como Entregado
            </button>`;
        } else if (isEntregado) {
            footerHtml = `
                <button onclick="updateStatusFromModal(${order.id}, 'finalizado')" class="w-full bg-blue-700 hover:bg-blue-800 active:scale-95 text-white font-extrabold py-3.5 rounded-xl text-base transition shadow-md flex items-center justify-center gap-2">
                    🔒 Finalizar Pedido
                </button>
                <button onclick="updateStatusFromModal(${order.id}, 'pendiente')" class="w-full bg-cream-dark hover:bg-cream-dark/80 active:scale-95 text-coffee-dark font-bold py-3 rounded-xl text-sm transition flex items-center justify-center gap-2">
                    🔄 Regresar a Pendiente
                </button>
            `;
        } else {
            footerHtml = `<div class="text-center py-3 text-coffee-medium font-extrabold text-sm bg-coffee-dark/5 rounded-xl border border-cream-dark">🔒 Pedido Finalizado — Sin acciones disponibles</div>`;
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
