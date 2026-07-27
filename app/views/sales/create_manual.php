<div class="max-w-4xl mx-auto space-y-6 animate-slide-up">
    <!-- Header -->
    <div class="bg-white p-6 rounded-2xl border border-cream-dark shadow-sm flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="font-heading font-extrabold text-3xl text-coffee-dark">Registrar Pedido Histórico</h1>
            <p class="text-sm text-coffee-light mt-1">Registra un pedido realizado en otra fecha (por ejemplo, en 2025) con productos e importes.</p>
        </div>
        <div>
            <a href="<?= BASE_URL ?>/sales/history" class="bg-cream-dark hover:bg-cream-dark/80 text-coffee-dark font-bold py-2.5 px-5 rounded-xl text-sm transition inline-flex items-center gap-1.5 active:scale-95">
                📋 Ver Historial
            </a>
        </div>
    </div>

    <!-- Main Form -->
    <form id="manual-sale-form" action="<?= BASE_URL ?>/sales/save-manual" method="POST" class="bg-white p-6 rounded-2xl border border-cream-dark shadow-sm space-y-6">
        <!-- Dynamic Alert Container -->
        <div id="alert-container" class="hidden"></div>

        <!-- Date & Payment Method Selector -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="sale_date" class="block text-sm font-bold text-coffee-dark mb-1">Fecha y Hora del Pedido *</label>
                <input type="datetime-local" id="sale_date" name="sale_date" required 
                       value="<?= date('Y-m-d\TH:i') ?>"
                       class="w-full px-4 py-3 rounded-xl border border-cream-dark focus:outline-none focus:ring-2 focus:ring-accent text-sm text-coffee-dark">
                <span class="text-xs text-coffee-light mt-1.5 block">
                    ⚠️ Al guardar el pedido, este se guardará automáticamente en estado <strong>Finalizado</strong>.
                </span>
            </div>

            <div>
                <label class="block text-sm font-bold text-coffee-dark mb-1">
                    Método de Pago <span class="text-rose-600">*</span>
                </label>
                <input type="hidden" name="payment_method" id="payment_method" value="" required>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" id="pm-btn-efectivo" onclick="selectPaymentMethod('efectivo')"
                            class="pm-btn flex items-center justify-center gap-2 py-3 px-4 rounded-xl font-bold text-sm border-2 border-cream-dark bg-cream hover:bg-cream-dark text-coffee-dark transition-all">
                        <span>💵</span> Efectivo
                    </button>
                    <button type="button" id="pm-btn-qr" onclick="selectPaymentMethod('qr')"
                            class="pm-btn flex items-center justify-center gap-2 py-3 px-4 rounded-xl font-bold text-sm border-2 border-cream-dark bg-cream hover:bg-cream-dark text-coffee-dark transition-all">
                        <span>📱</span> QR
                    </button>
                </div>
            </div>
        </div>

        <div class="border-t border-cream-dark pt-6">
            <h3 class="font-heading font-bold text-lg text-coffee-dark mb-4">Productos del Pedido</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="products-table">
                    <thead>
                        <tr class="bg-coffee-dark/5 text-coffee-dark font-heading font-bold text-xs uppercase border-b border-cream-dark">
                            <th class="p-3 pl-4">Producto</th>
                            <th class="p-3 w-32">Precio Unitario</th>
                            <th class="p-3 w-40 text-center">Cantidad</th>
                            <th class="p-3 w-32 text-right">Subtotal</th>
                            <th class="p-3 pr-4 text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-cream-dark text-sm text-slate-700" id="items-container">
                        <!-- Dynamic rows injected here -->
                    </tbody>
                </table>
            </div>

            <!-- Add Item Button -->
            <div class="mt-4">
                <button type="button" onclick="addRow()" 
                        class="bg-coffee-medium/10 hover:bg-coffee-medium/20 text-coffee-dark font-bold py-2.5 px-4 rounded-xl text-xs transition inline-flex items-center gap-1.5 active:scale-95">
                    ➕ Añadir Producto
                </button>
            </div>
        </div>

        <!-- Grand Total & Submit -->
        <div class="border-t border-cream-dark pt-6 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div class="bg-emerald-50 border border-emerald-100 rounded-xl px-5 py-3 self-start md:self-auto flex items-center gap-4">
                <span class="text-sm font-semibold text-emerald-900">Total a Registrar:</span>
                <span class="text-2xl font-extrabold text-emerald-700 font-heading" id="grand-total">Bs. 0.00</span>
            </div>
            <button type="submit" 
                    class="bg-accent hover:bg-accent-dark text-white font-heading font-extrabold py-3.5 px-8 rounded-xl text-sm transition shadow-md shadow-accent/20 active:scale-95">
                💾 Guardar Pedido Histórico
            </button>
        </div>
    </form>
</div>

<!-- Template for select options -->
<script>
    const availableProducts = <?= json_encode(array_values($products)) ?>;
    let selectedPaymentMethod = null;

    function selectPaymentMethod(method) {
        selectedPaymentMethod = method;
        document.getElementById('payment_method').value = method;

        const btnEf = document.getElementById('pm-btn-efectivo');
        const btnQr = document.getElementById('pm-btn-qr');

        if (method === 'efectivo') {
            btnEf.className = "pm-btn flex items-center justify-center gap-2 py-3 px-4 rounded-xl font-extrabold text-sm border-2 border-emerald-600 bg-emerald-600 text-white shadow-md transition-all scale-[1.02]";
            btnQr.className = "pm-btn flex items-center justify-center gap-2 py-3 px-4 rounded-xl font-bold text-sm border-2 border-cream-dark bg-cream hover:bg-cream-dark text-coffee-dark transition-all";
        } else if (method === 'qr') {
            btnQr.className = "pm-btn flex items-center justify-center gap-2 py-3 px-4 rounded-xl font-extrabold text-sm border-2 border-blue-600 bg-blue-600 text-white shadow-md transition-all scale-[1.02]";
            btnEf.className = "pm-btn flex items-center justify-center gap-2 py-3 px-4 rounded-xl font-bold text-sm border-2 border-cream-dark bg-cream hover:bg-cream-dark text-coffee-dark transition-all";
        }
    }

    function resetPaymentMethod() {
        selectedPaymentMethod = null;
        document.getElementById('payment_method').value = '';
        const btnEf = document.getElementById('pm-btn-efectivo');
        const btnQr = document.getElementById('pm-btn-qr');
        if (btnEf) btnEf.className = "pm-btn flex items-center justify-center gap-2 py-3 px-4 rounded-xl font-bold text-sm border-2 border-cream-dark bg-cream hover:bg-cream-dark text-coffee-dark transition-all";
        if (btnQr) btnQr.className = "pm-btn flex items-center justify-center gap-2 py-3 px-4 rounded-xl font-bold text-sm border-2 border-cream-dark bg-cream hover:bg-cream-dark text-coffee-dark transition-all";
    }

    function addRow() {
        const container = document.getElementById('items-container');
        const rowIndex = Date.now();
        const tr = document.createElement('tr');
        tr.id = `row-${rowIndex}`;
        tr.className = 'hover:bg-cream/10 transition';

        let optionsHtml = '<option value="">-- Seleccionar --</option>';
        availableProducts.forEach(p => {
            optionsHtml += `<option value="${p.id}" data-price="${p.price}">${p.category_icon} ${p.name} (Bs. ${parseFloat(p.price).toFixed(2)})</option>`;
        });

        tr.innerHTML = `
            <td class="p-3 pl-4">
                <select name="items_select[${rowIndex}]" onchange="onProductSelect(${rowIndex}, this)" required
                        class="prod-select w-full px-3 py-2 rounded-xl border border-cream-dark focus:outline-none focus:ring-2 focus:ring-accent text-sm text-coffee-dark bg-white">
                    ${optionsHtml}
                </select>
                <!-- Hidden inputs to send keys and values on post -->
                <input type="hidden" class="real-qty-input" name="items[${rowIndex}]" id="qty-real-${rowIndex}" value="0">
            </td>
            <td class="p-3">
                <span class="font-semibold text-coffee-medium" id="price-${rowIndex}">Bs. 0.00</span>
            </td>
            <td class="p-3">
                <div class="flex items-center justify-center gap-1">
                    <button type="button" onclick="updateQty(${rowIndex}, -1)"
                            class="w-8 h-8 rounded-lg bg-white border border-cream-dark text-sm font-bold text-coffee-medium hover:bg-red-50 hover:text-red-500 transition flex items-center justify-center">-</button>
                    <input type="number" id="qty-${rowIndex}" min="0" value="0" oninput="onQtyChange(${rowIndex}, this)"
                           class="w-12 text-center text-sm font-extrabold text-coffee-dark bg-transparent border-0 focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                    <button type="button" onclick="updateQty(${rowIndex}, 1)"
                            class="w-8 h-8 rounded-lg bg-white border border-cream-dark text-sm font-bold text-coffee-medium hover:bg-emerald-50 hover:text-emerald-600 transition flex items-center justify-center">+</button>
                </div>
            </td>
            <td class="p-3 text-right">
                <span class="font-extrabold text-coffee-dark" id="subtotal-${rowIndex}">Bs. 0.00</span>
            </td>
            <td class="p-3 pr-4 text-center">
                <button type="button" onclick="removeRow(${rowIndex})"
                        class="text-red-500 hover:text-red-700 transition font-bold text-lg" title="Quitar item">
                    &times;
                </button>
            </td>
        `;

        container.appendChild(tr);
    }

    function onProductSelect(rowIndex, selectEl) {
        const option = selectEl.options[selectEl.selectedIndex];
        const price = option ? parseFloat(option.getAttribute('data-price')) || 0 : 0;
        
        // Update product select name attribute to map to product ID
        const realQtyInput = document.getElementById(`qty-real-${rowIndex}`);
        if (option && option.value) {
            realQtyInput.name = `items[${option.value}]`;
        }

        document.getElementById(`price-${rowIndex}`).innerText = `Bs. ${price.toFixed(2)}`;
        
        // Reset qty to 1 on first selection
        const qtyInput = document.getElementById(`qty-${rowIndex}`);
        if (parseInt(qtyInput.value) === 0) {
            qtyInput.value = 1;
            realQtyInput.value = 1;
        }

        recalculateSubtotal(rowIndex);
    }

    function updateQty(rowIndex, delta) {
        const qtyInput = document.getElementById(`qty-${rowIndex}`);
        let qty = parseInt(qtyInput.value) || 0;
        qty = Math.max(0, qty + delta);
        qtyInput.value = qty;
        
        const realQtyInput = document.getElementById(`qty-real-${rowIndex}`);
        if (realQtyInput) realQtyInput.value = qty;

        recalculateSubtotal(rowIndex);
    }

    function onQtyChange(rowIndex, inputEl) {
        const qty = Math.max(0, parseInt(inputEl.value) || 0);
        inputEl.value = qty;

        const realQtyInput = document.getElementById(`qty-real-${rowIndex}`);
        if (realQtyInput) realQtyInput.value = qty;

        recalculateSubtotal(rowIndex);
    }

    function recalculateSubtotal(rowIndex) {
        const selectEl = document.querySelector(`#row-${rowIndex} select`);
        const option = selectEl.options[selectEl.selectedIndex];
        const price = option ? parseFloat(option.getAttribute('data-price')) || 0 : 0;
        
        const qty = parseInt(document.getElementById(`qty-${rowIndex}`).value) || 0;
        const subtotal = price * qty;

        document.getElementById(`subtotal-${rowIndex}`).innerText = `Bs. ${subtotal.toFixed(2)}`;
        calculateGrandTotal();
    }

    function removeRow(rowIndex) {
        document.getElementById(`row-${rowIndex}`).remove();
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let total = 0;
        const rows = document.querySelectorAll('#items-container tr');
        rows.forEach(row => {
            const rowIndex = row.id.split('-')[1];
            const selectEl = row.querySelector('select');
            const option = selectEl.options[selectEl.selectedIndex];
            const price = option ? parseFloat(option.getAttribute('data-price')) || 0 : 0;
            const qty = parseInt(document.getElementById(`qty-${rowIndex}`).value) || 0;
            total += price * qty;
        });

        document.getElementById('grand-total').innerText = `Bs. ${total.toFixed(2)}`;
    }

    // Add first row on load
    document.addEventListener('DOMContentLoaded', () => {
        addRow();

        // AJAX Form Submission
        document.getElementById('manual-sale-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const alertContainer = document.getElementById('alert-container');
            alertContainer.classList.add('hidden');
            alertContainer.className = 'hidden';

            if (!selectedPaymentMethod) {
                alertContainer.className = "bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm font-semibold mb-4 block";
                alertContainer.innerText = 'Debes seleccionar obligatoriamente un tipo de pago (Efectivo o QR).';
                alertContainer.classList.remove('hidden');
                alertContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                return;
            }

            const form = this;
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alertContainer.className = "bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-semibold mb-4 block";
                    alertContainer.innerText = data.message;
                    
                    // Clear products and payment method selection
                    document.getElementById('items-container').innerHTML = '';
                    resetPaymentMethod();
                    addRow();
                    calculateGrandTotal();
                } else {
                    alertContainer.className = "bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm font-semibold mb-4 block";
                    alertContainer.innerText = data.message;
                }
                alertContainer.classList.remove('hidden');
                alertContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            })
            .catch(err => {
                alertContainer.className = "bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm font-semibold mb-4 block";
                alertContainer.innerText = 'Error de red o conexión al servidor.';
                alertContainer.classList.remove('hidden');
                alertContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            });
        });
    });
</script>
