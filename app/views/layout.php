<?php
use Core\Auth;
$isLoggedIn = Auth::check();
$currentUser = Auth::user();
$currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Helper function to check links visibility based on role
$userRole = Auth::role();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dukes Fast Food POS</title>
    <!-- Outfit & Inter Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN v3.4 -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        coffee: {
                            dark: '#3D1C02',
                            medium: '#7B4F2E',
                            light: '#A0714F',
                        },
                        cream: {
                            DEFAULT: '#FFF8F0',
                            dark: '#F5E6D3',
                        },
                        accent: {
                            DEFAULT: '#E07B39',
                            dark: '#C96525',
                            success: '#2D6A4F',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* ── Prevent ALL horizontal scroll on mobile ────────────── */
        html {
            overflow-x: clip !important;
        }
        body {
            max-width: 100vw;
            overflow-x: clip !important;
        }
        /* Every block-level element is capped at 100% width */
        *, *::before, *::after {
            max-width: 100%;
            box-sizing: border-box;
        }
        /* Re-allow internal horizontal scroll for tables and code blocks */
        .overflow-x-auto, [class*="overflow-x-auto"] {
            max-width: 100% !important;
            overflow-x: auto !important;
        }
        /* ── Scrollbar ─────────────────── */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #FFF8F0; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #C5A07A; border-radius: 99px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #7B4F2E; }

        /* ── Animations ────────────────── */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes popIn {
            0%   { transform: scale(0.5); opacity: 0; }
            70%  { transform: scale(1.06); }
            100% { transform: scale(1); opacity: 1; }
        }
        .animate-fade-in  { animation: fadeIn  0.35s cubic-bezier(0.16,1,0.3,1) both; }
        .animate-slide-up { animation: slideUp 0.38s cubic-bezier(0.16,1,0.3,1) both; }
        .animate-pop      { animation: popIn   0.42s cubic-bezier(0.16,1,0.3,1) both; }

        /* ── Cards ─────────────────────── */
        .hover-card {
            transition: transform 0.3s cubic-bezier(0.16,1,0.3,1),
                        box-shadow 0.3s cubic-bezier(0.16,1,0.3,1),
                        border-color 0.3s ease;
        }
        .hover-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 32px -10px rgba(61,28,2,0.12);
            border-color: #E07B39;
        }

        /* ── Mobile cart badge pulse ───── */
        #mobile-cart-badge { transition: transform 0.2s cubic-bezier(0.34,1.56,0.64,1); }

        /* ── Product cards ─────────────── */
        .product-card { animation: fadeIn 0.3s cubic-bezier(0.16,1,0.3,1) both; }

        /* ── POS footer safe area ──────── */
        @supports (padding-bottom: env(safe-area-inset-bottom)) {
            #cart-sidebar { padding-bottom: env(safe-area-inset-bottom); }
        }

        /* ── Transition for category tabs  */
        .cat-btn { transition: all 0.2s cubic-bezier(0.16,1,0.3,1); }
    </style>
    <script>
        window.BASE_URL = '<?= BASE_URL ?>';
    </script>
</head>
<body class="bg-cream min-h-screen text-slate-800 flex flex-col font-sans">

    <?php if ($isLoggedIn && $name !== 'auth/login'): ?>
    <!-- Header Navigation Bar -->
    <header class="sticky top-0 z-50 bg-gradient-to-r from-[#2B1302] via-[#3D1C02] to-[#2B1302] border-b border-white/10 text-white shadow-xl backdrop-blur-md bg-opacity-95">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-4">
                
                <!-- Left: Brand / Logo -->
                <a href="<?= BASE_URL ?>/" class="flex items-center gap-3 group shrink-0 py-1.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent rounded-xl">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-accent to-accent-dark flex items-center justify-center text-xl shadow-lg shadow-accent/20 group-hover:scale-105 group-hover:rotate-3 transition-all duration-300 border border-white/20">
                        🍔
                    </div>
                    <div class="flex flex-col">
                        <span class="font-heading font-extrabold text-lg tracking-tight text-white leading-tight flex items-center gap-1.5">
                            DUKE'S
                            <span class="text-[10px] font-bold tracking-widest uppercase text-accent bg-accent/15 px-2 py-0.5 rounded-full border border-accent/30">
                                POS
                            </span>
                        </span>
                        <span class="text-[10px] text-cream-dark/60 font-medium tracking-wider uppercase">Fast Food System</span>
                    </div>
                </a>

                <!-- Center/Right Nav Area (Desktop) -->
                <div class="hidden lg:flex items-center gap-3">
                    <?php if ($userRole === 'admin' || $userRole === 'caja'): ?>
                    <!-- POS Venta: Always visible & highlighted -->
                    <a href="<?= BASE_URL ?>/pos"
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 flex items-center gap-2 shadow-lg shadow-accent/25 active:scale-95 bg-gradient-to-r from-accent to-accent-dark text-white hover:brightness-110 border border-white/20 focus:outline-none focus:ring-2 focus:ring-accent">
                        <span class="text-sm">🛒</span>
                        <span>POS Venta</span>
                    </a>
                    <?php endif; ?>

                    <!-- Categorized Custom Navigation Dropdowns / Comboboxes -->
                    <?php
                    // Define categories
                    $categories = [];

                    // Sales & Orders Category
                    $salesOps = [];
                    if ($userRole === 'admin') {
                        $salesOps[] = ['/'              , '📊', 'Dashboard'         , 'Resumen general de ventas'];
                    }
                    if ($userRole === 'admin' || $userRole === 'caja') {
                        $salesOps[] = ['/sales/create-manual', '📅', 'Pedido Histórico', 'Registro de ventas pasadas'];
                    }
                    if ($userRole === 'admin' || $userRole === 'caja' || $userRole === 'cocinero') {
                        $salesOps[] = ['/orders'        , '📋', 'Pedidos / Cocina'  , 'Gestión de comandas activas'];
                    }
                    if ($userRole === 'admin' || $userRole === 'caja') {
                        $salesOps[] = ['/sales/history' , '📜', 'Historial Ventas'  , 'Consulta de transacciones'];
                    }
                    if (!empty($salesOps)) {
                        $categories[] = ['id' => 'sales', 'title' => 'Ventas y Operación', 'icon' => '🧾', 'items' => $salesOps];
                    }

                    // Administration & Management Category
                    if ($userRole === 'admin') {
                        $adminOps = [
                            ['/products'     , '🍔', 'Gestión Productos', 'Catálogo, precios y menú'],
                            ['/raw-materials', '📦', 'Control Insumos'  , 'Stock e inventario'],
                            ['/users'        , '👤', 'Usuarios'          , 'Permisos y personal'],
                            ['/admin-expenses', '💸', 'Gastos Admin'    , 'Egresos y operaciones'],
                            ['/reports'      , '📈', 'Reportes General' , 'Estadísticas e informes'],
                            ['/settings'     , '⚙️', 'Configuración'     , 'Ajustes del sistema']
                        ];
                        $categories[] = ['id' => 'admin', 'title' => 'Gestión & Ajustes', 'icon' => '⚙️', 'items' => $adminOps];
                    }
                    ?>

                    <?php foreach ($categories as $cat):
                        $hasActive = false;
                        $activeLabel = $cat['title'];
                        foreach ($cat['items'] as [$path, $icon, $label, $desc]) {
                            if (($path === '/' && ($currentUri === BASE_URL . '/' || $currentUri === BASE_URL)) ||
                                ($path !== '/' && strpos($currentUri, BASE_URL . $path) === 0)) {
                                $hasActive = true;
                                $activeLabel = $label;
                                break;
                            }
                        }
                    ?>
                    <!-- Combobox Dropdown Container -->
                    <div class="relative dropdown-menu-container group">
                        <button type="button"
                                aria-expanded="false"
                                class="px-3.5 py-2 rounded-xl text-xs font-semibold flex items-center gap-2 transition-all duration-200 border shadow-sm focus:outline-none focus:ring-2 focus:ring-accent/50
                                       <?= $hasActive
                                           ? 'bg-gradient-to-r from-white/20 to-white/10 text-white font-bold border-white/30 shadow-inner'
                                           : 'bg-white/5 text-cream-dark/85 hover:bg-white/12 hover:text-white border-white/10 hover:border-white/20' ?>">
                            <span class="text-sm opacity-95"><?= $cat['icon'] ?></span>
                            <span class="tracking-tight"><?= $hasActive ? $activeLabel : $cat['title'] ?></span>
                            <?php if ($hasActive): ?>
                            <span class="w-1.5 h-1.5 rounded-full bg-accent animate-pulse"></span>
                            <?php endif; ?>
                            <svg class="w-3.5 h-3.5 opacity-60 transition-transform duration-200 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Floating Custom Menu Dropdown -->
                        <div class="absolute top-full left-0 mt-2 w-64 rounded-2xl bg-[#2B1302]/95 backdrop-blur-xl border border-white/15 shadow-2xl p-1.5 opacity-0 invisible translate-y-2 transition-all duration-200 z-50 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 group-focus-within:opacity-100 group-focus-within:visible group-focus-within:translate-y-0">
                            <div class="px-3 py-1.5 mb-1 border-b border-white/10 flex items-center justify-between">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-cream-dark/50"><?= $cat['title'] ?></span>
                                <span class="text-[10px] text-accent font-semibold"><?= count($cat['items']) ?> opciones</span>
                            </div>
                            <div class="space-y-0.5">
                                <?php foreach ($cat['items'] as [$path, $icon, $label, $desc]):
                                    $isSelected = ($path === '/')
                                        ? ($currentUri === BASE_URL . '/' || $currentUri === BASE_URL)
                                        : (strpos($currentUri, BASE_URL . $path) === 0);
                                ?>
                                <a href="<?= BASE_URL . $path ?>"
                                   class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs transition-all duration-150 group/item
                                          <?= $isSelected
                                                ? 'bg-gradient-to-r from-accent/25 to-accent/10 text-white font-bold border border-accent/30 shadow-sm'
                                                : 'text-cream-dark/80 hover:bg-white/10 hover:text-white border border-transparent' ?>">
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-sm bg-white/5 border border-white/10 group-hover/item:scale-110 transition-transform">
                                        <?= $icon ?>
                                    </div>
                                    <div class="flex flex-col flex-grow">
                                        <span class="text-xs font-semibold leading-tight"><?= $label ?></span>
                                        <span class="text-[10px] text-cream-dark/50 group-hover/item:text-cream-dark/75 transition-colors"><?= $desc ?></span>
                                    </div>
                                    <?php if ($isSelected): ?>
                                    <span class="w-1.5 h-1.5 rounded-full bg-accent"></span>
                                    <?php endif; ?>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Right: Profile, Role & Logout Action (Desktop) or Hamburger (Mobile) -->
                <div class="flex items-center gap-3 shrink-0">
                    <!-- User Profile info & Logout -->
                    <div class="hidden lg:flex items-center gap-3 pl-2 border-l border-white/10">
                        <div class="flex flex-col text-right">
                            <span class="text-xs font-medium text-cream-dark/90 leading-tight">
                                <?= e($currentUser['full_name']) ?>
                            </span>
                            <span class="text-[9px] text-amber-300 font-bold uppercase tracking-wider bg-amber-400/10 px-2 py-0.5 rounded-md border border-amber-400/20 w-max ml-auto mt-0.5">
                                <?= e($userRole) ?>
                            </span>
                        </div>
                        <a href="<?= BASE_URL ?>/logout"
                           title="Cerrar sesión"
                           class="text-xs bg-red-500/10 hover:bg-red-600 text-red-300 hover:text-white border border-red-500/20 hover:border-red-600 px-3 py-2 rounded-xl transition-all duration-200 font-semibold flex items-center gap-1.5 active:scale-95 focus:outline-none focus:ring-2 focus:ring-red-500">
                            <span>Salir</span>
                            <span>🚪</span>
                        </a>
                    </div>

                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-btn" type="button"
                            aria-label="Abrir menú de navegación"
                            class="lg:hidden relative w-10 h-10 rounded-xl bg-white/5 hover:bg-white/10 active:bg-white/20 border border-white/10 flex items-center justify-center transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent group">
                        <div class="w-5 h-4 relative flex flex-col justify-between items-center">
                            <span id="ham-line-1" class="w-5 h-0.5 bg-white rounded-full transition-all duration-300 origin-center"></span>
                            <span id="ham-line-2" class="w-5 h-0.5 bg-white rounded-full transition-all duration-300 origin-center"></span>
                            <span id="ham-line-3" class="w-5 h-0.5 bg-white rounded-full transition-all duration-300 origin-center"></span>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Overlay Menu Backdrop -->
        <div id="mobile-backdrop"
             class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm hidden lg:hidden transition-opacity duration-300"
             onclick="closeMobileMenu()"></div>

        <!-- Mobile Menu Drawer / Panel -->
        <div id="mobile-menu"
             class="fixed top-16 left-0 right-0 z-50 lg:hidden max-h-[calc(100vh-4rem)] overflow-y-auto custom-scrollbar
                    -translate-y-4 opacity-0 pointer-events-none
                    transition-all duration-300 ease-out">
            <div class="mx-3 my-2 rounded-2xl overflow-hidden shadow-2xl border border-white/15 backdrop-blur-xl"
                 style="background: linear-gradient(180deg, #2B1302 0%, #3D1C02 100%);">

                <!-- Mobile User Badge Header -->
                <div class="px-4 py-3 bg-white/5 border-b border-white/10 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-accent/20 border border-accent/40 flex items-center justify-center text-accent text-sm font-bold shadow-inner">
                            👤
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-white"><?= e($currentUser['full_name']) ?></span>
                            <span class="text-[10px] text-cream-dark/60">@<?= e($currentUser['username']) ?></span>
                        </div>
                    </div>
                    <span class="text-[10px] text-amber-300 font-bold uppercase tracking-wider bg-amber-400/15 px-2.5 py-1 rounded-lg border border-amber-400/30">
                        <?= e($userRole) ?>
                    </span>
                </div>

                <!-- Nav links -->
                <div class="p-3 space-y-1">
                    <?php
                    // Mobile navigation items
                    $mobileItems = [];
                    if ($userRole === 'admin' || $userRole === 'caja') {
                        $mobileItems[] = ['/pos', '🛒', 'POS Venta', true];
                    }
                    if ($userRole === 'admin') {
                        $mobileItems[] = ['/'              , '📊', 'Dashboard'          , false];
                    }
                    if ($userRole === 'admin' || $userRole === 'caja') {
                        $mobileItems[] = ['/sales/create-manual' , '📅', 'Pedido Histórico', false];
                    }
                    if ($userRole === 'admin' || $userRole === 'caja' || $userRole === 'cocinero') {
                        $mobileItems[] = ['/orders'        , '📋', 'Preparación Pedidos', false];
                    }
                    if ($userRole === 'admin') {
                        $mobileItems[] = ['/products'      , '🍔', 'Gestión Productos'  , false];
                        $mobileItems[] = ['/raw-materials' , '📦', 'Inventario Insumos' , false];
                        $mobileItems[] = ['/users'         , '👤', 'Control Usuarios'   , false];
                    }
                    if ($userRole === 'admin' || $userRole === 'caja') {
                        $mobileItems[] = ['/sales/history' , '📜', 'Historial Ventas'  , false];
                    }
                    if ($userRole === 'admin') {
                        $mobileItems[] = ['/admin-expenses', '💸', 'Gastos Admin', false];
                        $mobileItems[] = ['/reports'       , '📈', 'Reportes', false];
                        $mobileItems[] = ['/settings'      , '⚙️', 'Configuración'      , false];
                    }

                    foreach ($mobileItems as [$path, $icon, $label, $isHighlight]):
                        $isActive = ($path === '/')
                            ? ($currentUri === BASE_URL . '/' || $currentUri === BASE_URL)
                            : (strpos($currentUri, BASE_URL . $path) === 0);
                    ?>
                    <a href="<?= BASE_URL . $path ?>"
                       onclick="closeMobileMenu()"
                       class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-semibold transition-all duration-150 active:scale-[0.98]
                              <?= $isHighlight
                                    ? 'bg-gradient-to-r from-accent to-accent-dark text-white shadow-md border border-white/20 font-bold'
                                    : ($isActive
                                        ? 'bg-white/15 text-white font-bold border border-white/15'
                                        : 'text-cream-dark/80 hover:bg-white/10 hover:text-white border border-transparent') ?>">
                        <span class="text-xl w-7 text-center"><?= $icon ?></span>
                        <span class="flex-grow"><?= $label ?></span>
                        <?php if ($isActive && !$isHighlight): ?>
                        <span class="w-2 h-2 rounded-full bg-accent shadow-sm"></span>
                        <?php endif; ?>
                    </a>
                    <?php endforeach; ?>
                </div>

                <!-- Mobile Menu Footer / Logout -->
                <div class="p-3 border-t border-white/10 bg-black/20">
                    <a href="<?= BASE_URL ?>/logout"
                       class="w-full text-sm bg-red-600/20 hover:bg-red-600 border border-red-500/30 text-red-200 hover:text-white px-4 py-2.5 rounded-xl font-bold transition-all duration-200 flex items-center justify-center gap-2 active:scale-[0.98]">
                        <span>Cerrar Sesión</span>
                        <span>🚪</span>
                    </a>
                </div>
            </div>
        </div>
    </header>
    <?php endif; ?>

    <!-- Main Content Container -->
    <main class="flex-grow max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8">
        
        <!-- Status Notifications (Flash Messages) -->
        <?php if ($success = Auth::getFlash('success')): ?>
            <div class="mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-xl flex items-center justify-between shadow-sm animate-fade-in">
                <div class="flex items-center gap-2">
                    <span class="text-xl">✅</span>
                    <span class="font-medium"><?= e($success) ?></span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold">&times;</button>
            </div>
        <?php endif; ?>

        <?php if ($error = Auth::getFlash('error')): ?>
            <div class="mb-6 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded-r-xl flex items-center justify-between shadow-sm animate-fade-in">
                <div class="flex items-center gap-2">
                    <span class="text-xl">⚠️</span>
                    <span class="font-medium"><?= e($error) ?></span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 font-bold">&times;</button>
            </div>
        <?php endif; ?>

        <!-- Insert Child View Content -->
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="bg-coffee-dark/5 border-t border-coffee-dark/10 py-6 text-center text-xs text-coffee-medium">
        <p>&copy; <?= date('Y') ?> Duke's Fast Food POS. Todos los derechos reservados. Diseñado con ❤️ para Comida Rápida.</p>
    </footer>

    <!-- Global Javascript -->
    <script>
        (function () {
            const btn    = document.getElementById('mobile-menu-btn');
            const menu   = document.getElementById('mobile-menu');
            const back   = document.getElementById('mobile-backdrop');
            const line1  = document.getElementById('ham-line-1');
            const line2  = document.getElementById('ham-line-2');
            const line3  = document.getElementById('ham-line-3');
            let isOpen = false;

            window.closeMobileMenu = function () {
                if (!isOpen) return;
                isOpen = false;
                menu.classList.remove('translate-y-0', 'opacity-100', 'pointer-events-auto');
                menu.classList.add('-translate-y-4', 'opacity-0', 'pointer-events-none');
                back.classList.add('hidden');
                if (line1) { line1.style.top='14px'; line1.style.transform=''; }
                if (line2) { line2.style.top='20px'; line2.style.transform=''; }
                if (line3) { line3.style.opacity='1'; line3.style.transform=''; }
            };

            function openMenu() {
                isOpen = true;
                back.classList.remove('hidden');
                menu.classList.remove('-translate-y-4', 'opacity-0', 'pointer-events-none');
                menu.classList.add('translate-y-0', 'opacity-100', 'pointer-events-auto');
                if (line1) { line1.style.top='20px'; line1.style.transform='rotate(45deg)'; }
                if (line2) { line2.style.top='20px'; line2.style.transform='rotate(-45deg)'; }
                if (line3) { line3.style.opacity='0'; line3.style.transform='scaleX(0)'; }
            }

            if (btn) btn.addEventListener('click', () => isOpen ? closeMobileMenu() : openMenu());
        })();
    </script>
</body>
</html>
