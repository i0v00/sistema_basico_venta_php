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
    <!-- Top Navigation Bar (Single Row Desktop Layout) -->
    <header class="bg-coffee-dark border-b border-coffee-medium/20 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between gap-4">
            
            <!-- Left: Brand / Logo -->
            <a href="<?= BASE_URL ?>/" class="flex items-center space-x-2.5 group shrink-0">
                <span class="text-2xl transition-transform duration-300 group-hover:scale-110">🍔</span>
                <span class="font-heading font-extrabold text-lg tracking-tight text-white">
                    DUKE'S <span class="text-accent bg-accent/10 px-2 py-0.5 rounded-lg border border-accent/20">Fast Food</span>
                </span>
            </a>

            <!-- Center: Navigation Tabs (Desktop only) -->
            <nav class="hidden lg:flex items-center space-x-2">
                <?php if ($userRole === 'admin'): ?>
                <a href="<?= BASE_URL ?>/" class="px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 <?= $currentUri === BASE_URL . '/' || $currentUri === BASE_URL ? 'bg-white/15 text-white' : 'text-cream-dark/70 hover:bg-white/5 hover:text-white' ?>">
                    📊 Dashboard
                </a>
                <?php endif; ?>

                <?php if ($userRole === 'admin' || $userRole === 'caja'): ?>
                <a href="<?= BASE_URL ?>/pos" class="px-4 py-2 bg-accent hover:bg-accent-dark text-white rounded-xl text-sm font-bold transition-all duration-200 flex items-center gap-1.5 shadow-sm active:scale-95">
                    🛒 POS Venta
                </a>
                <a href="<?= BASE_URL ?>/sales/create-manual" class="px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 <?= $currentUri === BASE_URL . '/sales/create-manual' ? 'bg-white/15 text-white' : 'text-cream-dark/70 hover:bg-white/5 hover:text-white' ?>">
                    📅 Pedido Histórico
                </a>
                <?php endif; ?>

                <?php if ($userRole === 'admin' || $userRole === 'caja' || $userRole === 'cocinero'): ?>
                <a href="<?= BASE_URL ?>/orders" class="px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 <?= strpos($currentUri, BASE_URL . '/orders') === 0 ? 'bg-white/15 text-white' : 'text-cream-dark/70 hover:bg-white/5 hover:text-white' ?>">
                    📋 Pedidos
                </a>
                <?php endif; ?>

                <?php if ($userRole === 'admin'): ?>
                <a href="<?= BASE_URL ?>/products" class="px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 <?= strpos($currentUri, BASE_URL . '/products') === 0 ? 'bg-white/15 text-white' : 'text-cream-dark/70 hover:bg-white/5 hover:text-white' ?>">
                    🍔 Productos
                </a>
                <a href="<?= BASE_URL ?>/raw-materials" class="px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 <?= strpos($currentUri, BASE_URL . '/raw-materials') === 0 ? 'bg-white/15 text-white' : 'text-cream-dark/70 hover:bg-white/5 hover:text-white' ?>">
                    📦 Insumos
                </a>
                <a href="<?= BASE_URL ?>/users" class="px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 <?= strpos($currentUri, BASE_URL . '/users') === 0 ? 'bg-white/15 text-white' : 'text-cream-dark/70 hover:bg-white/5 hover:text-white' ?>">
                    👤 Usuarios
                </a>
                <?php endif; ?>

                <?php if ($userRole === 'admin' || $userRole === 'caja'): ?>
                <a href="<?= BASE_URL ?>/sales/history" class="px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 <?= $currentUri === BASE_URL . '/sales/history' ? 'bg-white/15 text-white' : 'text-cream-dark/70 hover:bg-white/5 hover:text-white' ?>">
                    📜 Historial
                </a>
                <?php endif; ?>

                <?php if ($userRole === 'admin'): ?>
                <a href="<?= BASE_URL ?>/settings" class="px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 <?= $currentUri === BASE_URL . '/settings' ? 'bg-white/15 text-white' : 'text-cream-dark/70 hover:bg-white/5 hover:text-white' ?>">
                    ⚙️ Config
                </a>
                <?php endif; ?>
            </nav>

            <!-- Right: Profile, Role & Logout Action (Desktop) or Hamburger (Mobile) -->
            <div class="flex items-center gap-4 shrink-0">
                <!-- User Profile info & Logout -->
                <div class="hidden lg:flex items-center space-x-3">
                    <div class="flex flex-col text-right">
                        <span class="text-xs text-cream-dark/80">Hola, <strong class="text-white"><?= e($currentUser['full_name']) ?></strong></span>
                        <span class="text-[9px] text-accent font-bold uppercase tracking-wider bg-accent/10 px-2 py-0.5 rounded-full border border-accent/20 w-max ml-auto mt-0.5"><?= e($userRole) ?></span>
                    </div>
                    <a href="<?= BASE_URL ?>/logout" class="text-xs bg-red-600/10 hover:bg-red-600 text-red-400 hover:text-white border border-red-500/20 px-3.5 py-2 rounded-xl transition-all font-bold active:scale-95">
                        Salir 🚪
                    </a>
                </div>

                <!-- Mobile Menu Button (lg:hidden) - shifted slightly left -->
                <button id="mobile-menu-btn" type="button"
                        class="lg:hidden relative w-11 h-11 rounded-xl bg-white/5 border border-white/10 hover:bg-white/15 flex items-center justify-center transition-all duration-200 focus:outline-none active:scale-95 mr-1">
                    <span id="ham-line-1" class="absolute w-6 h-[2px] bg-white rounded-full transition-all duration-300" style="top:14px; left:10px"></span>
                    <span id="ham-line-2" class="absolute w-6 h-[2px] bg-white rounded-full transition-all duration-300" style="top:20px; left:10px"></span>
                    <span id="ham-line-3" class="absolute w-6 h-[2px] bg-white rounded-full transition-all duration-300" style="top:26px; left:10px"></span>
                </button>
            </div>
        </div>

        <!-- Mobile Overlay Menu Backdrop -->
        <div id="mobile-backdrop"
             class="fixed inset-0 z-[45] bg-black/40 backdrop-blur-[2px] hidden lg:hidden"
             onclick="closeMobileMenu()"></div>

        <!-- Slide-down panel (Mobile only) -->
        <div id="mobile-menu"
             class="fixed top-16 left-0 right-0 z-[46] lg:hidden
                    -translate-y-4 opacity-0 pointer-events-none
                    transition-all duration-300 ease-out">
            <div class="mx-3 mt-1 rounded-2xl overflow-hidden shadow-2xl border border-white/10"
                 style="background: linear-gradient(180deg, #2e1502 0%, #3D1C02 100%);">

                <!-- Nav links -->
                <div class="px-3 pt-3 pb-2 space-y-1">
                    <?php
                    $mobileLinks = [];
                    if ($userRole === 'admin') {
                        $mobileLinks[] = ['/'              , '📊', 'Dashboard'          , false];
                    }
                    if ($userRole === 'admin' || $userRole === 'caja') {
                        $mobileLinks[] = ['/pos'           , '🛒', 'Registrar Venta (POS)', true ];
                        $mobileLinks[] = ['/sales/create-manual' , '📅', 'Pedido Histórico', false];
                    }
                    if ($userRole === 'admin' || $userRole === 'caja' || $userRole === 'cocinero') {
                        $mobileLinks[] = ['/orders'        , '📋', 'Preparación Pedidos', false];
                    }
                    if ($userRole === 'admin') {
                        $mobileLinks[] = ['/products'      , '🍔', 'Gestión Productos'  , false];
                        $mobileLinks[] = ['/raw-materials' , '📦', 'Inventario Insumos' , false];
                        $mobileLinks[] = ['/users'         , '👤', 'Control Usuarios'   , false];
                    }
                    if ($userRole === 'admin' || $userRole === 'caja') {
                        $mobileLinks[] = ['/sales/history' , '📜', 'Historial Ventas'  , false];
                    }
                    if ($userRole === 'admin') {
                        $mobileLinks[] = ['/settings'      , '⚙️', 'Configuración'      , false];
                    }

                    foreach ($mobileLinks as [$path, $icon, $label, $isPrimary]):
                        $isActive = ($path === '/')
                            ? ($currentUri === BASE_URL.'/' || $currentUri === BASE_URL)
                            : (strpos($currentUri, BASE_URL.$path) === 0);
                    ?>
                    <a href="<?= BASE_URL . $path ?>"
                       onclick="closeMobileMenu()"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-150 active:scale-[0.98]
                              <?= $isPrimary
                                    ? 'bg-accent text-white shadow-md'
                                    : ($isActive
                                        ? 'bg-white/15 text-white'
                                        : 'text-cream-dark/80 hover:bg-white/10 hover:text-white') ?>">
                        <span class="text-lg w-7 text-center"><?= $icon ?></span>
                        <span class="flex-grow"><?= $label ?></span>
                        <?php if ($isActive && !$isPrimary): ?>
                        <span class="w-1.5 h-1.5 rounded-full bg-accent"></span>
                        <?php endif; ?>
                    </a>
                    <?php endforeach; ?>
                </div>

                <!-- User footer -->
                <div class="px-4 py-3 border-t border-white/10 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-accent/20 border border-accent/40 flex items-center justify-center text-xs">👤</div>
                        <span class="text-xs text-cream-dark/70">Sesión: <strong class="text-white"><?= e($currentUser['username']) ?></strong></span>
                    </div>
                    <a href="<?= BASE_URL ?>/logout"
                       class="text-xs bg-red-900/80 hover:bg-red-700 border border-red-700/40 px-3.5 py-2 rounded-xl text-white font-bold transition flex items-center gap-1 active:scale-95">
                        Salir 🚪
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
