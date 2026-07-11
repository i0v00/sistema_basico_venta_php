<div class="space-y-6 animate-slide-up">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-cream-dark shadow-sm">
        <div>
            <h1 class="font-heading font-extrabold text-3xl text-coffee-dark">Gestionar Usuarios</h1>
            <p class="text-sm text-coffee-light mt-1">Crea, edita y administra los accesos al sistema.</p>
        </div>
        <div>
            <a href="<?= BASE_URL ?>/users/create" class="inline-flex items-center gap-2 bg-accent hover:bg-accent-dark text-white font-bold py-3 px-5 rounded-xl text-sm transition shadow-sm active:scale-95">
                ➕ Crear Usuario
            </a>
        </div>
    </div>

    <!-- Users Table/Grid -->
    <div class="bg-white rounded-2xl border border-cream-dark shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-coffee-dark/5 text-coffee-dark font-heading font-bold text-sm border-b border-cream-dark">
                        <th class="p-4 pl-6">ID</th>
                        <th class="p-4">Nombre Completo</th>
                        <th class="p-4">Usuario</th>
                        <th class="p-4">Rol</th>
                        <th class="p-4">Estado</th>
                        <th class="p-4 pr-6 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-cream-dark text-sm text-slate-700">
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="p-8 text-center text-coffee-light">No hay usuarios registrados.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr class="hover:bg-cream/20 transition-all">
                                <td class="p-4 pl-6 font-semibold text-coffee-light">#<?= $user['id'] ?></td>
                                <td class="p-4">
                                    <div class="font-bold text-coffee-dark"><?= e($user['full_name']) ?></div>
                                </td>
                                <td class="p-4 text-slate-600 font-mono"><?= e($user['username']) ?></td>
                                <td class="p-4">
                                    <?php if ($user['role'] === 'admin'): ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">👑 Administrador</span>
                                    <?php elseif ($user['role'] === 'caja'): ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-800 border border-blue-200">💵 Caja / POS</span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">🍳 Cocinero</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4">
                                    <?php if ((int)$user['active'] === 1): ?>
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Activo
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-rose-500">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Inactivo
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4 pr-6 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="<?= BASE_URL ?>/users/edit?id=<?= $user['id'] ?>" class="p-2 text-coffee-medium hover:bg-cream-dark/50 rounded-xl transition" title="Editar">
                                            ✏️
                                        </a>
                                        <form action="<?= BASE_URL ?>/users/delete" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar a este usuario?');">
                                            <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                            <button type="submit" class="p-2 text-red-600 hover:bg-rose-50 rounded-xl transition" title="Eliminar">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
