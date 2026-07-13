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
            <p class="text-sm text-coffee-light mt-1">Control de pedidos y entregas en tiempo real. Adaptado para celular y tablets.</p>
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

<script>
    let currentFilter = 'all';
    let previousOrdersCount = -1;

    // Fetch and render orders from API
    async function loadOrders() {
        try {
            const response = await fetch(`${window.BASE_URL}/orders/json?status=${currentFilter}`);
            const data = await response.json();
            
            if (data.success) {
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
            orderCard.className = `bg-white rounded-2xl border ${cardBorder} overflow-hidden transition-all duration-300 flex flex-col justify-between`;
            orderCard.id = `order-${order.id}`;

            // Build items list with larger fonts & "unid."
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

            // Action button based on state
            let actionBtn = '';
            if (isPend) {
                actionBtn = `<button onclick="updateStatus(${order.id}, 'entregado')" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold py-3.5 px-4 rounded-xl text-base transition shadow-sm active:scale-95 flex items-center justify-center gap-1.5">
                       ✅ Listo / Entregar
                   </button>`;
            } else if (isEntregado) {
                actionBtn = `
                    <div class="flex flex-col sm:flex-row gap-2">
                        <button onclick="updateStatus(${order.id}, 'finalizado')" class="flex-1 bg-blue-700 hover:bg-blue-800 text-white font-extrabold py-3.5 px-2 rounded-xl text-base transition shadow-sm active:scale-95 flex items-center justify-center gap-1">
                            🔒 Finalizar
                        </button>
                        <button onclick="updateStatus(${order.id}, 'pendiente')" class="flex-1 bg-cream-dark hover:bg-cream-dark/80 text-coffee-dark font-bold py-3.5 px-2 rounded-xl text-base transition active:scale-95 flex items-center justify-center gap-1">
                            🔄 Regresar
                        </button>
                    </div>
                `;
            } else if (isFinalizado) {
                actionBtn = `<div class="text-center py-3 text-coffee-medium font-extrabold text-sm bg-coffee-dark/5 rounded-xl border border-cream-dark">
                    🔒 Pedido Finalizado
                </div>`;
            }

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

    // Call update API
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
