<?php
use Core\Auth;
$isLoggedIn = Auth::check();
$currentUser = Auth::user();
$currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dukes Cakes POS</title>
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
    <!-- Top Navigation Bar -->
    <nav class="bg-coffee-dark text-white sticky top-0 z-40 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand & Logo -->
                <div class="flex items-center">
                    <a href="<?= BASE_URL ?>/" class="flex items-center space-x-2">
                        <span class="text-2xl">🍔</span>
                        <span class="font-heading font-extrabold text-xl tracking-tight text-white">
                            DUKE'S <span class="text-accent">CAKES</span>
                        </span>
                    </a>
                </div>

                <!-- Desktop Nav Items -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="<?= BASE_URL ?>/" class="px-3 py-2 rounded-lg text-sm font-medium transition <?= $currentUri === BASE_URL . '/' || $currentUri === BASE_URL ? 'bg-coffee-medium text-white' : 'text-cream-dark hover:bg-coffee-medium/40 hover:text-white' ?>">
                        📊 Dashboard
                    </a>
                    <a href="<?= BASE_URL ?>/pos" class="px-4 py-2 bg-accent hover:bg-accent-dark text-white rounded-lg text-sm font-bold transition flex items-center gap-1 shadow-sm">
                        🛒 POS Venta
                    </a>
                    <a href="<?= BASE_URL ?>/products" class="px-3 py-2 rounded-lg text-sm font-medium transition <?= strpos($currentUri, BASE_URL . '/products') === 0 ? 'bg-coffee-medium text-white' : 'text-cream-dark hover:bg-coffee-medium/40 hover:text-white' ?>">
                        🍔 Productos
                    </a>
                    <a href="<?= BASE_URL ?>/raw-materials" class="px-3 py-2 rounded-lg text-sm font-medium transition <?= strpos($currentUri, BASE_URL . '/raw-materials') === 0 ? 'bg-coffee-medium text-white' : 'text-cream-dark hover:bg-coffee-medium/40 hover:text-white' ?>">
                        📦 Inventario
                    </a>
                    <a href="<?= BASE_URL ?>/sales/history" class="px-3 py-2 rounded-lg text-sm font-medium transition <?= $currentUri === BASE_URL . '/sales/history' ? 'bg-coffee-medium text-white' : 'text-cream-dark hover:bg-coffee-medium/40 hover:text-white' ?>">
                        📜 Historial
                    </a>
                    <a href="<?= BASE_URL ?>/settings" class="px-3 py-2 rounded-lg text-sm font-medium transition <?= $currentUri === BASE_URL . '/settings' ? 'bg-coffee-medium text-white' : 'text-cream-dark hover:bg-coffee-medium/40 hover:text-white' ?>">
                        ⚙️ Config
                    </a>
                </div>

                <!-- Admin user profile & logout -->
                <div class="hidden md:flex items-center space-x-4 border-l border-coffee-medium pl-4">
                    <span class="text-xs text-cream-dark">Hola, <strong class="text-white"><?= e($currentUser['username']) ?></strong></span>
                    <a href="<?= BASE_URL ?>/logout" class="text-xs bg-red-800 hover:bg-red-700 px-3 py-1.5 rounded-lg transition font-medium">
                        Salir 🚪
                    </a>
                </div>

                <!-- Mobile Menu Button with animated icon -->
                <div class="flex md:hidden items-center gap-3">
                    <span class="text-[10px] text-cream-dark/70"><?= e($currentUser['username']) ?></span>
                    <button id="mobile-menu-btn" type="button"
                            class="relative w-10 h-10 rounded-xl bg-coffee-medium/30 hover:bg-coffee-medium/60 flex items-center justify-center transition-all duration-200 focus:outline-none active:scale-90">
                        <!-- Hamburger lines → animated to X -->
                        <span id="ham-line-1" class="absolute w-5 h-0.5 bg-white rounded-full transition-all duration-300" style="top:14px"></span>
                        <span id="ham-line-2" class="absolute w-5 h-0.5 bg-white rounded-full transition-all duration-300" style="top:19px"></span>
                        <span id="ham-line-3" class="absolute w-4 h-0.5 bg-white/60 rounded-full transition-all duration-300" style="top:24px;left:12px"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Overlay Menu (fixed, does NOT push content) -->
        <!-- Backdrop -->
        <div id="mobile-backdrop"
             class="fixed inset-0 z-[45] bg-black/50 backdrop-blur-[2px] hidden md:hidden"
             onclick="closeMobileMenu()"></div>

        <!-- Slide-down panel -->
        <div id="mobile-menu"
             class="fixed top-16 left-0 right-0 z-[46] md:hidden
                    -translate-y-4 opacity-0 pointer-events-none
                    transition-all duration-300 ease-out">
            <div class="mx-3 mt-1 rounded-2xl overflow-hidden shadow-2xl border border-white/10"
                 style="background: linear-gradient(180deg, #2e1502 0%, #3D1C02 100%);">

                <!-- Nav links -->
                <div class="px-3 pt-3 pb-2 space-y-1">
                    <?php
                    $mobileLinks = [
                        ['/'              , '📊', 'Dashboard'          , false],
                        ['/pos'           , '🛒', 'Registrar Venta'    , true ],
                        ['/products'      , '🍔', 'Productos'          , false],
                        ['/raw-materials' , '📦', 'Inventario'         , false],
                        ['/sales/history' , '📜', 'Historial de Ventas', false],
                        ['/settings'      , '⚙️', 'Configuración'      , false],
                    ];
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
                       class="text-xs bg-red-900/80 hover:bg-red-700 border border-red-700/40 px-3 py-1.5 rounded-lg text-white font-medium transition flex items-center gap-1">
                        Salir 🚪
                    </a>
                </div>
            </div>
        </div>
    </nav>
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
        <p>&copy; <?= date('Y') ?> Duke's Cakes POS. Todos los derechos reservados. Diseñado con ❤️ para Comida Rápida.</p>
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
                if (line2) { line2.style.top='19px'; line2.style.transform=''; }
                if (line3) { line3.style.opacity='1'; line3.style.transform=''; }
            };

            function openMenu() {
                isOpen = true;
                back.classList.remove('hidden');
                menu.classList.remove('-translate-y-4', 'opacity-0', 'pointer-events-none');
                menu.classList.add('translate-y-0', 'opacity-100', 'pointer-events-auto');
                if (line1) { line1.style.top='19px'; line1.style.transform='rotate(45deg)'; }
                if (line2) { line2.style.top='19px'; line2.style.transform='rotate(-45deg)'; }
                if (line3) { line3.style.opacity='0'; line3.style.transform='scaleX(0)'; }
            }

            if (btn) btn.addEventListener('click', () => isOpen ? closeMobileMenu() : openMenu());
        })();
    </script>
</body>
</html>
