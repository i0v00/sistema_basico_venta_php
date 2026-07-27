<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-heading font-extrabold text-coffee-dark">Historial de Ventas</h1>
        <p class="text-coffee-light">Consulta y revisa las transacciones registradas por el restaurante</p>
    </div>

    <?php if ($flash = getFlash('success')): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-semibold">
            <?= e($flash) ?>
        </div>
    <?php endif; ?>
    <?php if ($flash = getFlash('error')): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm font-semibold">
            <?= e($flash) ?>
        </div>
    <?php endif; ?>

    <!-- Date Filters + Export Actions -->
    <div class="bg-white p-4 rounded-2xl border border-cream-dark shadow-sm space-y-3">
        <form method="GET" action="<?= BASE_URL ?>/sales/history" id="history-filter-form" class="flex flex-col sm:flex-row gap-4 items-end">
            <div class="w-full sm:flex-1">
                <label for="start_date" class="block text-xs font-bold text-coffee-medium mb-1 uppercase">Fecha Inicio</label>
                <input type="date" id="start_date" name="start_date" value="<?= e($startDate) ?>"
                       class="w-full px-4 py-2 rounded-xl border border-cream-dark focus:outline-none focus:ring-2 focus:ring-accent text-sm">
            </div>
            <div class="w-full sm:flex-1">
                <label for="end_date" class="block text-xs font-bold text-coffee-medium mb-1 uppercase">Fecha Término</label>
                <input type="date" id="end_date" name="end_date" value="<?= e($endDate) ?>"
                       class="w-full px-4 py-2 rounded-xl border border-cream-dark focus:outline-none focus:ring-2 focus:ring-accent text-sm">
            </div>
            <div class="flex gap-2 sm:shrink-0">
                <button type="submit" class="bg-accent hover:bg-accent-dark text-white font-bold px-6 py-2.5 rounded-xl transition duration-200 text-sm shadow-sm flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Filtrar
                </button>
            </div>
        </form>

        <!-- Export Buttons Row -->
        <div class="flex flex-wrap items-center gap-2 pt-1 border-t border-cream-dark">
            <span class="text-xs font-bold text-coffee-medium uppercase tracking-wide">Exportar:</span>
            <a id="btn-export-csv" href="<?= BASE_URL ?>/sales/export-csv?start_date=<?= e($startDate) ?>&end_date=<?= e($endDate) ?>"
               class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 transition active:scale-95 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="8" y1="13" x2="16" y2="13"/><line x1="8" y1="17" x2="16" y2="17"/></svg>
                Descargar CSV
            </a>
            <a id="btn-export-excel" href="<?= BASE_URL ?>/sales/export-excel?start_date=<?= e($startDate) ?>&end_date=<?= e($endDate) ?>"
               class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-xl text-xs font-bold bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 transition active:scale-95 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><polyline points="8 10 10 14 12 10 14 14 16 10"/></svg>
                Descargar Excel
            </a>
            <span class="text-[10px] text-slate-400 font-medium ml-1"><?= count($sales) ?> registros en el período</span>
        </div>
    </div>

    <script>
    // Keep export links in sync when filter dates change
    (function() {
        const sd = document.getElementById('start_date');
        const ed = document.getElementById('end_date');
        const csvBtn   = document.getElementById('btn-export-csv');
        const excelBtn = document.getElementById('btn-export-excel');
        const base     = window.BASE_URL;

        function updateExportLinks() {
            const q = '?start_date=' + (sd.value || '') + '&end_date=' + (ed.value || '');
            if (csvBtn)   csvBtn.href   = base + '/sales/export-csv'   + q;
            if (excelBtn) excelBtn.href = base + '/sales/export-excel' + q;
        }
        if (sd) sd.addEventListener('change', updateExportLinks);
        if (ed) ed.addEventListener('change', updateExportLinks);
    })();
    </script>

    <!-- Sales List -->
    <div class="bg-white rounded-2xl border border-cream-dark overflow-hidden shadow-sm animate-fade-in">
        <?php if (empty($sales)): ?>
            <div class="p-12 text-center text-slate-400">
                <span class="text-5xl block mb-2">📜</span>
                <p>No se encontraron registros de ventas para el período seleccionado.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-coffee-dark/5 text-coffee-dark border-b border-cream-dark text-xs uppercase font-bold tracking-wider">
                            <th class="p-4 pl-6">N° Ticket</th>
                            <th class="p-4">Fecha y Hora</th>
                            <th class="p-4 text-center">Método Pago</th>
                            <th class="p-4 text-center">Cantidad Items</th>
                            <th class="p-4 text-right">Total Vendido</th>
                            <th class="p-4 pr-6 text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-cream-dark text-sm">
                        <?php foreach ($sales as $sale): ?>
                            <?php 
                            $pm = strtolower($sale['payment_method'] ?? '');
                            $isQr = ($pm === 'qr');
                            $isEf = ($pm === 'efectivo');
                            $isUnspecified = (!$isQr && !$isEf);
                            ?>
                            <tr class="hover:bg-cream/10 transition">
                                <td class="p-4 pl-6 font-bold text-coffee-dark">#<?= $sale['id'] ?></td>
                                <td class="p-4 text-slate-500 font-medium"><?= date('d/m/Y H:i', strtotime($sale['sale_date'])) ?></td>
                                <td class="p-4 text-center">
                                    <?php if ($isUnspecified): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                            ⚠️ Sin Especificar
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold <?= $isQr ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200' ?>">
                                            <?= $isQr ? '📱 QR' : '💵 Efectivo' ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4 text-center font-semibold text-coffee-medium"><?= (int)$sale['items_count'] ?></td>
                                <td class="p-4 text-right font-extrabold text-coffee-dark"><?= formatMoney($sale['total']) ?></td>
                                <td class="p-4 pr-6">
                                    <div class="text-center flex justify-center items-center gap-2">
                                        <button onclick="loadSaleDetails(<?= $sale['id'] ?>, '<?= date('d/m/Y H:i', strtotime($sale['sale_date'])) ?>', '<?= formatMoney($sale['total']) ?>')" 
                                                 class="bg-coffee-medium/10 hover:bg-coffee-medium/20 text-coffee-dark text-xs font-bold px-4 py-2 rounded-lg transition duration-200">
                                             🔍 Ver Detalle
                                         </button>
                                         <?php if (\Core\Auth::role() === 'admin'): ?>
                                         <button type="button" onclick="confirmDeleteSale(<?= $sale['id'] ?>)" class="bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold px-3 py-2 rounded-lg transition duration-200">
                                             ❌ Eliminar
                                         </button>
                                         <?php endif; ?>
                                     </div>
                                 </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Details -->
<div id="details-modal" class="fixed inset-0 bg-coffee-dark/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl w-full md:w-[30%] md:min-w-[320px] border border-cream-dark shadow-2xl overflow-hidden animate-fade-in">
        <div class="bg-coffee-dark text-white p-4 flex items-center justify-between">
            <h4 class="font-heading font-extrabold text-base" id="details-modal-title">Detalle del Ticket</h4>
            <button onclick="closeDetailsModal()" class="text-white font-bold text-lg">&times;</button>
        </div>
        
        <div class="p-6 space-y-4">
            <div class="text-xs text-slate-500 flex justify-between font-medium">
                <span id="details-modal-date">Fecha: --/--/----</span>
                <span id="details-modal-id" class="font-bold text-coffee-dark">Ticket N°: --</span>
            </div>

            <!-- Payment Method Container -->
            <div id="details-modal-pm-container" class="bg-slate-50 p-3 rounded-xl border border-cream-dark text-xs flex flex-col gap-2">
                <!-- Javascript dynamic insert for payment method status or assign form -->
            </div>

            <!-- List items -->
            <div class="border-t border-b border-cream-dark/50 py-3 max-h-60 overflow-y-auto custom-scrollbar space-y-2" id="details-modal-items">
                <!-- Javascript dynamic insert -->
            </div>

            <!-- Total -->
            <div class="flex justify-between items-center text-base font-extrabold text-coffee-dark pt-2">
                <span>Total de Venta:</span>
                <span id="details-modal-total" class="text-lg text-accent-dark font-heading">$0.00</span>
            </div>

            <button onclick="closeDetailsModal()" class="w-full bg-coffee-medium hover:bg-coffee-dark text-white font-bold py-3 rounded-xl transition duration-200 text-xs">
                Cerrar Detalle
            </button>
        </div>
    </div>
</div>

<script>
    function loadSaleDetails(saleId, dateStr, totalStr) {
        const modal = document.getElementById("details-modal");
        const itemsContainer = document.getElementById("details-modal-items");
        const pmContainer = document.getElementById("details-modal-pm-container");
        
        document.getElementById("details-modal-title").innerText = `Ticket #${saleId}`;
        document.getElementById("details-modal-date").innerText = `Fecha: ${dateStr}`;
        document.getElementById("details-modal-id").innerText = `Ticket N°: #${saleId}`;
        document.getElementById("details-modal-total").innerText = totalStr;

        itemsContainer.innerHTML = `<div class="text-center text-slate-400 py-6 text-xs">Cargando detalles...</div>`;
        pmContainer.innerHTML = `<div class="text-center text-slate-400 py-1 text-xs">Cargando información de pago...</div>`;
        modal.classList.remove("hidden");

        // Fetch details from backend api
        fetch(`${window.BASE_URL}/sales/details?id=${saleId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Payment Method section
                    if (data.payment_method === 'efectivo') {
                        pmContainer.innerHTML = `
                            <div class="flex justify-between items-center font-semibold">
                                <span class="text-coffee-light uppercase tracking-wider text-[10px]">Método de Pago:</span>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    💵 Efectivo
                                </span>
                            </div>
                        `;
                    } else if (data.payment_method === 'qr') {
                        pmContainer.innerHTML = `
                            <div class="flex justify-between items-center font-semibold">
                                <span class="text-coffee-light uppercase tracking-wider text-[10px]">Método de Pago:</span>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                    📱 QR
                                </span>
                            </div>
                        `;
                    } else {
                        const currentUri = encodeURIComponent(window.location.pathname + window.location.search);
                        pmContainer.innerHTML = `
                            <div class="flex flex-col gap-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-coffee-light uppercase tracking-wider text-[10px] font-bold">Método de Pago:</span>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                        ⚠️ Sin Especificar (null)
                                    </span>
                                </div>
                                <div class="text-[11px] text-coffee-medium font-medium">Designar método de pago para este ticket:</div>
                                <div class="flex gap-2">
                                    <button type="button" onclick="assignPaymentMethod(${saleId}, 'efectivo')" 
                                            class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold py-1.5 px-3 rounded-lg transition text-center flex items-center justify-center gap-1 shadow-sm">
                                        💵 Asignar Efectivo
                                    </button>
                                    <button type="button" onclick="assignPaymentMethod(${saleId}, 'qr')" 
                                            class="flex-1 bg-blue-500 hover:bg-blue-600 text-white text-xs font-bold py-1.5 px-3 rounded-lg transition text-center flex items-center justify-center gap-1 shadow-sm">
                                        📱 Asignar QR
                                    </button>
                                </div>
                            </div>
                        `;
                    }

                    // Details items section
                    if (data.details) {
                        itemsContainer.innerHTML = "";
                        data.details.forEach(item => {
                            const div = document.createElement("div");
                            div.className = "flex justify-between text-xs py-1 border-b border-slate-50 last:border-0";
                            div.innerHTML = `
                                <span class="text-coffee-dark font-medium">${item.product_icon} ${item.product_name} <strong class="text-slate-400">x${item.quantity}</strong></span>
                                <span class="font-bold text-coffee-medium">$${(parseFloat(item.price) * parseInt(item.quantity)).toFixed(2)}</span>
                            `;
                            itemsContainer.appendChild(div);
                        });
                    }
                } else {
                    itemsContainer.innerHTML = `<div class="text-center text-rose-600 py-6 text-xs">Error al cargar detalles.</div>`;
                    pmContainer.innerHTML = `<div class="text-center text-rose-600 py-1 text-xs">Error.</div>`;
                }
            })
            .catch(err => {
                itemsContainer.innerHTML = `<div class="text-center text-rose-600 py-6 text-xs">Error de conexión.</div>`;
                pmContainer.innerHTML = `<div class="text-center text-rose-600 py-1 text-xs">Error de conexión.</div>`;
            });
    }

    function assignPaymentMethod(saleId, method) {
        fetch(`${window.BASE_URL}/sales/update-payment-method`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `sale_id=${saleId}&payment_method=${method}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const btn = document.querySelector(`button[onclick^="loadSaleDetails(${saleId},"]`);
                if (btn) {
                    const tr = btn.closest('tr');
                    if (tr) {
                        const pmCell = tr.querySelectorAll('td')[2];
                        if (pmCell) {
                            if (method === 'efectivo') {
                                pmCell.innerHTML = `<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">💵 Efectivo</span>`;
                            } else {
                                pmCell.innerHTML = `<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">📱 QR</span>`;
                            }
                        }
                    }
                }
                
                const totalStr = document.getElementById("details-modal-total").innerText;
                const dateStr = document.getElementById("details-modal-date").innerText.replace("Fecha: ", "");
                loadSaleDetails(saleId, dateStr, totalStr);
            } else {
                alert(data.message || 'Error al asignar método de pago');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error de conexión al asignar el método de pago');
        });
    }

    function closeDetailsModal() {
        document.getElementById("details-modal").classList.add("hidden");
    }
</script>

<!-- Modal Delete Confirmation (Premium Design) -->
<div id="delete-modal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-md z-50 flex items-center justify-center hidden p-4 transition-all duration-300">
    <div class="bg-white rounded-3xl w-full md:w-[30%] md:min-w-[320px] border border-red-100 shadow-2xl overflow-hidden transform transition-all duration-300 scale-95 hover:scale-100 animate-fade-in relative">
        <!-- Close button top right -->
        <button onclick="closeDeleteModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 bg-slate-100 hover:bg-slate-200 w-8 h-8 rounded-full flex items-center justify-center transition font-bold text-sm z-10">&times;</button>
        
        <div class="p-6 text-center space-y-5">
            <!-- Icon Hero -->
            <div class="w-16 h-16 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center mx-auto shadow-inner border border-red-200/60">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>

            <div>
                <h3 class="text-xl font-heading font-extrabold text-slate-900" id="delete-modal-title">¿Eliminar Ticket #00?</h3>
                <p class="text-slate-500 text-xs mt-1.5 leading-relaxed font-medium">
                    Esta venta será anulada y eliminada lógicamente de los registros del sistema.
                </p>
            </div>

            <!-- Danger notice banner -->
            <div class="bg-red-50/80 border border-red-100 text-red-700 p-3 rounded-2xl text-xs font-semibold flex items-center gap-2 text-left">
                <span class="text-base flex-shrink-0">⚠️</span>
                <span>¿Estás seguro de que deseas proceder con la anulación?</span>
            </div>

            <!-- Buttons Action -->
            <div class="flex items-center gap-3 pt-2">
                <button type="button" onclick="closeDeleteModal()" class="flex-1 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-700 font-bold py-3 px-4 rounded-xl transition text-xs">
                    Cancelar
                </button>
                <button type="button" id="confirm-delete-btn" class="flex-1 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-bold py-3 px-4 rounded-xl transition text-xs shadow-lg shadow-red-500/30 active:scale-95 flex items-center justify-center gap-2">
                    <span>🗑️</span> Sí, Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // --- Delete Modal Logic ---
    let currentSaleIdToDelete = null;

    function confirmDeleteSale(saleId) {
        currentSaleIdToDelete = saleId;
        const titleEl = document.getElementById('delete-modal-title');
        if (titleEl) titleEl.innerText = `¿Eliminar Ticket #${saleId}?`;

        const modal = document.getElementById('delete-modal');
        if (modal) modal.classList.remove('hidden');
    }

    function closeDeleteModal() {
        currentSaleIdToDelete = null;
        const modal = document.getElementById('delete-modal');
        if (modal) modal.classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const confirmBtn = document.getElementById('confirm-delete-btn');
        if (!confirmBtn) return;

        confirmBtn.addEventListener('click', function() {
            if (!currentSaleIdToDelete) return;
            const saleId = currentSaleIdToDelete;
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<span>⏳</span> Eliminando...';

            fetch(`${window.BASE_URL}/sales/delete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `sale_id=${saleId}`
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = '<span>🗑️</span> Sí, Eliminar';
                closeDeleteModal();
                
                if (data.success) {
                    const delBtn = document.querySelector(`button[onclick="confirmDeleteSale(${saleId})"]`);
                    if (delBtn) {
                        const row = delBtn.closest('tr');
                        if (row) {
                            row.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                            setTimeout(() => row.remove(), 500);
                        }
                    }
                } else {
                    alert(data.message || 'Error al eliminar el pedido.');
                }
            })
            .catch(err => {
                console.error(err);
                btn.disabled = false;
                btn.innerHTML = '<span>🗑️</span> Sí, Eliminar';
                alert('Error de conexión al eliminar el pedido.');
                closeDeleteModal();
            });
        });
    });
</script>

