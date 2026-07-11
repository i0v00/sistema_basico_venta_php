<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-2xl shadow-xl border border-cream-dark">
        <div class="text-center">
            <span class="text-6xl inline-block animate-bounce mb-3">🍔</span>
            <h2 class="font-heading font-extrabold text-3xl text-coffee-dark tracking-tight">
                Duke's Cakes
            </h2>
            <p class="mt-2 text-sm text-coffee-light">
                Inicia sesión en el panel del restaurante
            </p>
        </div>

        <form class="mt-8 space-y-6" action="<?= BASE_URL ?>/login" method="POST">
            <div class="space-y-4">
                <div>
                    <label for="username" class="block text-sm font-semibold text-coffee-dark">Usuario</label>
                    <input id="username" name="username" type="text" required 
                           class="mt-1 appearance-none rounded-xl relative block w-full px-4 py-3 border border-cream-dark placeholder-slate-400 text-coffee-dark focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm" 
                           placeholder="admin">
                </div>
                <div>
                    <label for="password" class="block text-sm font-semibold text-coffee-dark">Contraseña</label>
                    <input id="password" name="password" type="password" required 
                           class="mt-1 appearance-none rounded-xl relative block w-full px-4 py-3 border border-cream-dark placeholder-slate-400 text-coffee-dark focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm" 
                           placeholder="••••••••">
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-accent hover:bg-accent-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent transition duration-200 shadow-md">
                    Ingresar al POS 🔑
                </button>
            </div>
        </form>
    </div>
</div>
