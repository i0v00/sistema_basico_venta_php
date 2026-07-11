<div class="min-h-[85vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-cream">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-3xl shadow-2xl border border-cream-dark transition-all duration-300 hover:shadow-coffee-dark/5">
        <div class="text-center">
            <span class="text-7xl inline-block animate-bounce mb-3 drop-shadow-md">🍔</span>
            <h2 class="font-heading font-extrabold text-4xl text-coffee-dark tracking-tight">
                Duke's Cakes
            </h2>
            <div class="w-16 h-1 bg-accent mx-auto mt-3 rounded-full"></div>
            <p class="mt-3 text-sm text-coffee-light">
                Inicia sesión para ingresar al sistema
            </p>
        </div>

        <form class="mt-8 space-y-6" action="<?= BASE_URL ?>/login" method="POST">
            <div class="space-y-4">
                <div>
                    <label for="username" class="block text-sm font-semibold text-coffee-medium mb-1">Usuario</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-lg text-coffee-light/60">👤</span>
                        <input id="username" name="username" type="text" required 
                               class="appearance-none rounded-2xl relative block w-full pl-10 pr-4 py-3.5 border border-cream-dark placeholder-slate-400 text-coffee-dark bg-cream/30 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm transition-all duration-200" 
                               placeholder="Ingresa tu usuario">
                    </div>
                </div>
                <div>
                    <label for="password" class="block text-sm font-semibold text-coffee-medium mb-1">Contraseña</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-lg text-coffee-light/60">🔑</span>
                        <input id="password" name="password" type="password" required 
                               class="appearance-none rounded-2xl relative block w-full pl-10 pr-4 py-3.5 border border-cream-dark placeholder-slate-400 text-coffee-dark bg-cream/30 focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm transition-all duration-200" 
                               placeholder="••••••••">
                    </div>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" 
                        class="group relative w-full flex justify-center py-4 px-4 border border-transparent text-sm font-extrabold rounded-2xl text-white bg-accent hover:bg-accent-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent transition duration-200 shadow-lg shadow-accent/25 hover:shadow-accent/40 active:scale-[0.98]">
                    Ingresar al POS 🔑
                </button>
            </div>
        </form>
    </div>
</div>
