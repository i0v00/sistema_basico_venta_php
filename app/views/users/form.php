<?php
$isEdit = isset($editUser);
?>
<div class="max-w-xl mx-auto space-y-6 animate-slide-up">
    <!-- Header -->
    <div class="bg-white p-6 rounded-2xl border border-cream-dark shadow-sm">
        <h1 class="font-heading font-extrabold text-2xl text-coffee-dark">
            <?= $isEdit ? '✏️ Editar Usuario' : '➕ Crear Nuevo Usuario' ?>
        </h1>
        <p class="text-xs text-coffee-light mt-1">
            <?= $isEdit ? 'Modifica los datos del usuario seleccionado.' : 'Registra una nueva cuenta para tu personal.' ?>
        </p>
    </div>

    <!-- Form Card -->
    <div class="bg-white p-8 rounded-2xl border border-cream-dark shadow-sm">
        <form action="<?= BASE_URL ?>/users/save" method="POST" class="space-y-5">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= $editUser['id'] ?>">
            <?php endif; ?>

            <!-- Username -->
            <div>
                <label for="username" class="block text-sm font-semibold text-coffee-medium mb-1">Nombre de Usuario (Login)</label>
                <input type="text" id="username" name="username" required
                       class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-cream-dark placeholder-slate-400 text-coffee-dark focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm"
                       placeholder="Ej: juan.perez"
                       value="<?= e($editUser['username'] ?? '') ?>">
            </div>

            <!-- Full Name -->
            <div>
                <label for="full_name" class="block text-sm font-semibold text-coffee-medium mb-1">Nombre Completo</label>
                <input type="text" id="full_name" name="full_name" required
                       class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-cream-dark placeholder-slate-400 text-coffee-dark focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm"
                       placeholder="Ej: Juan Pérez Morales"
                       value="<?= e($editUser['full_name'] ?? '') ?>">
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-semibold text-coffee-medium mb-1">
                    Contraseña <?= $isEdit ? '<span class="text-xs font-normal text-slate-400">(déjala en blanco si no deseas cambiarla)</span>' : '' ?>
                </label>
                <input type="password" id="password" name="password" <?= $isEdit ? '' : 'required' ?>
                       class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-cream-dark placeholder-slate-400 text-coffee-dark focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm"
                       placeholder="••••••••">
            </div>

            <!-- Role Select -->
            <div>
                <label for="role" class="block text-sm font-semibold text-coffee-medium mb-1">Rol en el Sistema</label>
                <select id="role" name="role" required
                        class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-cream-dark text-coffee-dark bg-white focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm">
                    <option value="caja" <?= (isset($editUser) && $editUser['role'] === 'caja') ? 'selected' : '' ?>>💵 Caja (Registrar Ventas y ver Pedidos)</option>
                    <option value="cocinero" <?= (isset($editUser) && $editUser['role'] === 'cocinero') ? 'selected' : '' ?>>🍳 Cocinero (Visualizar y Gestionar Pedidos)</option>
                    <option value="admin" <?= (isset($editUser) && $editUser['role'] === 'admin') ? 'selected' : '' ?>>👑 Administrador (Acceso Total)</option>
                </select>
            </div>

            <!-- Active Status (Only for edit) -->
            <?php if ($isEdit): ?>
                <div>
                    <label for="active" class="block text-sm font-semibold text-coffee-medium mb-1">Estado de Cuenta</label>
                    <select id="active" name="active" required
                            class="appearance-none rounded-xl relative block w-full px-4 py-3 border border-cream-dark text-coffee-dark bg-white focus:outline-none focus:ring-2 focus:ring-accent focus:border-transparent text-sm">
                        <option value="1" <?= (int)$editUser['active'] === 1 ? 'selected' : '' ?>>✅ Activo (Puede iniciar sesión)</option>
                        <option value="0" <?= (int)$editUser['active'] === 0 ? 'selected' : '' ?>>❌ Inactivo (Bloqueado)</option>
                    </select>
                </div>
            <?php endif; ?>

            <!-- Action buttons -->
            <div class="flex gap-4 pt-3">
                <button type="submit"
                        class="flex-1 bg-accent hover:bg-accent-dark text-white font-bold py-3.5 px-4 rounded-xl text-sm transition shadow-sm active:scale-95 text-center">
                    💾 Guardar Cambios
                </button>
                <a href="<?= BASE_URL ?>/users"
                   class="flex-1 bg-cream-dark/50 hover:bg-cream-dark text-coffee-dark font-bold py-3.5 px-4 rounded-xl text-sm transition text-center active:scale-95">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
