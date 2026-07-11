<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-heading font-extrabold text-coffee-dark">Historial de Ventas</h1>
        <p class="text-coffee-light">Consulta y revisa las transacciones registradas por el restaurante</p>
    </div>

    <!-- Date Filters -->
    <div class="bg-white p-4 rounded-2xl border border-cream-dark shadow-sm">
        <form method="GET" action="<?= BASE_URL ?>/sales/history" class="flex flex-col sm:flex-row gap-4 items-center">
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
            <div class="w-full sm:w-auto pt-5">
                <button type="submit" class="w-full bg-accent hover:bg-accent-dark text-white font-bold px-8 py-2.5 rounded-xl transition duration-200 text-sm shadow-sm">
                    Filtrar 📊
                </button>
            </div>
        </form>
    </div>

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
                            <th class="p-4 text-center">Cantidad Items</th>
                            <th class="p-4 text-right">Total Vendido</th>
                            <th class="p-4 pr-6 text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-cream-dark text-sm">
                        <?php foreach ($sales as $sale): ?>
                            <tr class="hover:bg-cream/10 transition">
                                <td class="p-4 pl-6 font-bold text-coffee-dark">#<?= $sale['id'] ?></td>
                                <td class="p-4 text-slate-500 font-medium"><?= date('d/m/Y H:i', strtotime($sale['sale_date'])) ?></td>
                                <td class="p-4 text-center font-semibold text-coffee-medium"><?= (int)$sale['items_count'] ?></td>
                                <td class="p-4 text-right font-extrabold text-coffee-dark"><?= formatMoney($sale['total']) ?></td>
                                <td class="p-4 pr-6">
                                    <div class="text-center">
                                        <button onclick="loadSaleDetails(<?= $sale['id'] ?>, '<?= date('d/m/Y H:i', strtotime($sale['sale_date'])) ?>', '<?= formatMoney($sale['total']) ?>')" 
                                                class="bg-coffee-medium/10 hover:bg-coffee-medium/20 text-coffee-dark text-xs font-bold px-4 py-2 rounded-lg transition duration-200">
                                            🔍 Ver Detalle
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Details -->
<div id="details-modal" class="fixed inset-0 bg-coffee-dark/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl w-full max-w-md border border-cream-dark shadow-2xl overflow-hidden animate-fade-in">
        <div class="bg-coffee-dark text-white p-4 flex items-center justify-between">
            <h4 class="font-heading font-extrabold text-base" id="details-modal-title">Detalle del Ticket</h4>
            <button onclick="closeDetailsModal()" class="text-white font-bold text-lg">&times;</button>
        </div>
        
        <div class="p-6 space-y-4">
            <div class="text-xs text-slate-500 flex justify-between font-medium">
                <span id="details-modal-date">Fecha: --/--/----</span>
                <span id="details-modal-id" class="font-bold text-coffee-dark">Ticket N°: --</span>
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
        
        document.getElementById("details-modal-title").innerText = `Ticket #${saleId}`;
        document.getElementById("details-modal-date").innerText = `Fecha: ${dateStr}`;
        document.getElementById("details-modal-id").innerText = `Ticket N°: #${saleId}`;
        document.getElementById("details-modal-total").innerText = totalStr;

        itemsContainer.innerHTML = `<div class="text-center text-slate-400 py-6 text-xs">Cargando detalles...</div>`;
        modal.classList.remove("hidden");

        // Fetch details from backend api
        fetch(`${window.BASE_URL}/sales/details?id=${saleId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.details) {
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
                } else {
                    itemsContainer.innerHTML = `<div class="text-center text-rose-600 py-6 text-xs">Error al cargar detalles.</div>`;
                }
            })
            .catch(err => {
                itemsContainer.innerHTML = `<div class="text-center text-rose-600 py-6 text-xs">Error de conexión.</div>`;
            });
    }

    function closeDetailsModal() {
        document.getElementById("details-modal").classList.add("hidden");
    }
</script>
