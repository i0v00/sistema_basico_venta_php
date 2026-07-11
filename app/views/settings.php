<?php
use Core\Auth;
$user = Auth::user();
$trackMaterials = (getSetting('track_raw_materials', '0') === '1');
?>

<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-heading font-extrabold text-coffee-dark">Configuración General</h1>
        <p class="text-coffee-light">Configura los parámetros del sistema y cambia tus credenciales de acceso</p>
    </div>

    <!-- Grid Forms -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Left Box: System Settings -->
        <div class="bg-white rounded-2xl border border-cream-dark p-6 sm:p-8 shadow-sm flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">⚙️</span>
                    <h3 class="font-heading font-bold text-lg text-coffee-dark">Opciones del POS</h3>
                </div>
                <p class="text-xs text-coffee-light">Controla el comportamiento general de la caja y descuento de inventario.</p>
                <hr class="border-cream-dark/50">

                <form action="<?= BASE_URL ?>/settings/save" method="POST" class="space-y-6">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" id="track_raw_materials" name="track_raw_materials" value="1" <?= $trackMaterials ? 'checked' : '' ?>
                                   class="w-4 h-4 text-accent border-cream-dark rounded focus:ring-accent">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="track_raw_materials" class="font-bold text-coffee-dark block">Descontar Materia Prima Automáticamente</label>
                            <span class="text-xs text-slate-500 block mt-0.5 leading-relaxed">
                                Si está activado, al registrar una venta se descontarán del inventario las materias primas asociadas a la receta del producto vendido.
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-coffee-medium hover:bg-coffee-dark text-white font-bold py-3 rounded-xl transition duration-200 text-xs shadow-sm">
                        Guardar Configuración de Caja 💾
                    </button>
                </form>
            </div>
            
            <div class="mt-6 bg-cream/35 p-4 rounded-xl border border-cream-dark/50 text-xs text-coffee-medium leading-relaxed">
                <span class="font-bold text-coffee-dark block mb-1">ℹ️ Nota sobre recetas:</span>
                El control de inventario requiere que configures las materias primas en el módulo correspondientes y actives la casilla de "Controlar Inventario" en la ficha de cada producto.
            </div>
        </div>

        <!-- Right Box: Credentials Settings -->
        <div class="bg-white rounded-2xl border border-cream-dark p-6 sm:p-8 shadow-sm space-y-4">
            <div class="flex items-center gap-2">
                <span class="text-2xl">🔒</span>
                <h3 class="font-heading font-bold text-lg text-coffee-dark">Seguridad de Acceso</h3>
            </div>
            <p class="text-xs text-coffee-light">Modifica el nombre de usuario administrador y/o actualiza la contraseña de inicio de sesión.</p>
            <hr class="border-cream-dark/50">

            <form action="<?= BASE_URL ?>/settings/change-password" method="POST" class="space-y-4">
                <!-- Username -->
                <div>
                    <label for="username" class="block text-xs font-bold text-coffee-medium uppercase">Usuario Administrador</label>
                    <input type="text" id="username" name="username" value="<?= e($user['username']) ?>" required
                           class="mt-1 w-full px-4 py-2.5 rounded-xl border border-cream-dark focus:outline-none focus:ring-2 focus:ring-accent text-sm font-semibold">
                </div>

                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-xs font-bold text-coffee-medium uppercase">Contraseña Actual</label>
                    <input type="password" id="current_password" name="current_password" required placeholder="••••••••"
                           class="mt-1 w-full px-4 py-2.5 rounded-xl border border-cream-dark focus:outline-none focus:ring-2 focus:ring-accent text-sm">
                    <span class="text-[10px] text-slate-400 mt-0.5 block">Requerido para guardar cualquier cambio.</span>
                </div>

                <!-- New Password -->
                <div>
                    <label for="new_password" class="block text-xs font-bold text-coffee-medium uppercase">Nueva Contraseña (Opcional)</label>
                    <input type="password" id="new_password" name="new_password" placeholder="Dejar en blanco para no cambiar"
                           class="mt-1 w-full px-4 py-2.5 rounded-xl border border-cream-dark focus:outline-none focus:ring-2 focus:ring-accent text-sm">
                </div>

                <button type="submit" class="w-full bg-accent hover:bg-accent-dark text-white font-bold py-3 rounded-xl transition duration-200 text-xs shadow-md">
                    Actualizar Credenciales 🔑
                </button>
            </form>
        </div>

    </div>
</div>
