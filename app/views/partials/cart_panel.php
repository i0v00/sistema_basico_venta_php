<!-- ══════════════════════════════════════════════════
     CART PANEL PARTIAL — shared by desktop + mobile
══════════════════════════════════════════════════ -->

<!-- Header -->
<div class="shrink-0 px-5 py-4 border-b border-cream-dark flex items-center justify-between bg-coffee-dark text-white">
    <div class="flex items-center gap-2.5">
        <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <span class="font-heading font-extrabold text-sm tracking-wide">Orden de Compra</span>
    </div>
    <!-- Close only on mobile sheet -->
    <button onclick="closeMobCart()"
            class="lg:hidden w-7 h-7 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
</div>

<!-- Cart items list -->
<div id="cart-items" class="flex-1 overflow-y-auto custom-scrollbar px-4 py-3 space-y-2">
    <!-- Filled by JS -->
</div>

<!-- Footer totals & actions -->
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
                class="flex items-center justify-center gap-1.5 py-3 rounded-xl
                       border border-cream-dark bg-white hover:bg-cream text-coffee-medium
                       text-xs font-bold transition-all active:scale-95">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Vaciar
        </button>
        <button onclick="openCheckout()"
                class="flex items-center justify-center gap-1.5 py-3 rounded-xl
                       bg-accent hover:bg-accent-dark text-white
                       text-xs font-extrabold transition-all shadow-md shadow-accent/20 active:scale-95">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                      d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V5a2 2 0 00-2-2H4a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Cobrar
        </button>
    </div>
</div>
