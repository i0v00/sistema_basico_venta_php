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
        html { overflow-x: clip !important; }
        body { max-width: 100vw; overflow-x: clip !important; }
        /* Box sizing but NEVER cap width on positioned/absolute elements */
        *, *::before, *::after { box-sizing: border-box; }
        /* Only cap width on block-level flow elements, not positioned */
        :not([style*="position"]):not(.absolute):not(.fixed):not([class*="absolute"]):not([class*="fixed"]) {
            max-width: 100%;
        }
        .overflow-x-auto, [class*="overflow-x-auto"] { max-width: 100% !important; overflow-x: auto !important; }

        /* ── Scrollbar ─────────────────── */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #FFF8F0; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #C5A07A; border-radius: 99px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #7B4F2E; }

        /* ── Animations ────────────────── */
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(40px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
        @keyframes popIn { 0% { transform: scale(0.5); opacity: 0; } 70% { transform: scale(1.06); } 100% { transform: scale(1); opacity: 1; } }
        .animate-fade-in  { animation: fadeIn  0.35s cubic-bezier(0.16,1,0.3,1) both; }
        .animate-slide-up { animation: slideUp 0.38s cubic-bezier(0.16,1,0.3,1) both; }
        .animate-pop      { animation: popIn   0.42s cubic-bezier(0.16,1,0.3,1) both; }

        /* ── Cards ─────────────────────── */
        .hover-card { transition: transform 0.3s cubic-bezier(0.16,1,0.3,1), box-shadow 0.3s cubic-bezier(0.16,1,0.3,1), border-color 0.3s ease; }
        .hover-card:hover { transform: translateY(-4px); box-shadow: 0 16px 32px -10px rgba(61,28,2,0.12); border-color: #E07B39; }

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

        /* ── Nav Dropdown ─────────────────────────────────────────
           IMPORTANT: The dropdown panel uses position:fixed so it
           is never clipped by any overflow:hidden ancestor.
        ────────────────────────────────────────────────────────── */
        .nav-dropdown-panel {
            position: fixed;
            top: 64px; /* header height */
            z-index: 9999;
            min-width: 240px;
            max-width: 280px;
            border-radius: 1rem;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.6), 0 0 0 1px rgba(255,255,255,0.08);
            background: linear-gradient(160deg, #1a0800 0%, #311505 100%);
            border: 1px solid rgba(255,255,255,0.1);
            padding: 6px;
            /* Hidden by default */
            opacity: 0;
            visibility: hidden;
            transform: translateY(-6px) scale(0.97);
            transform-origin: top left;
            transition:
                opacity 0.18s cubic-bezier(0.16,1,0.3,1),
                visibility 0.18s cubic-bezier(0.16,1,0.3,1),
                transform 0.22s cubic-bezier(0.16,1,0.3,1);
            pointer-events: none;
        }
        .nav-dropdown-panel.open {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
            pointer-events: auto;
        }
        .nav-dropdown-btn { transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease; }
        .nav-dropdown-btn.active-tab { background: rgba(255,255,255,0.15) !important; }
        .nav-chevron { transition: transform 0.2s ease; }
        .nav-chevron.rotated { transform: rotate(180deg); }

        /* ── Mobile Accordion ────────── */
        .mobile-acc-body {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s cubic-bezier(0.16,1,0.3,1);
        }
        .mobile-acc-body.open { max-height: 600px; }
        .mobile-acc-chevron { transition: transform 0.25s ease; }
        .mobile-acc-chevron.open { transform: rotate(180deg); }
    </style>
    <script>
        window.BASE_URL = '<?= BASE_URL ?>';
    </script>
</head>
<body class="bg-cream min-h-screen text-slate-800 flex flex-col font-sans">

    <?php if ($isLoggedIn && $name !== 'auth/login'): ?>
    <!-- ═══════════════════════════════════════════════════════════
         HEADER NAVIGATION BAR
    ═══════════════════════════════════════════════════════════ -->
    <header class="sticky top-0 z-50 bg-gradient-to-r from-[#1e0d01] via-[#2e1402] to-[#1e0d01] border-b border-white/8 text-white shadow-2xl">
        <div class="max-w-[70%] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-3">

                <!-- ── Brand / Logo ── -->
                <a href="<?= BASE_URL ?>/" class="flex items-center gap-2.5 group shrink-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-accent rounded-xl">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-accent to-accent-dark flex items-center justify-center shadow-lg shadow-accent/30 group-hover:scale-105 group-hover:rotate-3 transition-all duration-300 border border-white/15">
                        <!-- Burger SVG icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 12h18M3 6h18M3 18h18"/>
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-heading font-extrabold text-base tracking-tight text-white leading-none flex items-center gap-1.5">
                            DUKE'S
                            <span class="text-[9px] font-bold tracking-widest uppercase text-accent bg-accent/15 px-1.5 py-0.5 rounded-full border border-accent/30">POS</span>
                        </span>
                        <span class="text-[9px] text-white/40 font-medium tracking-wider uppercase mt-0.5">Fast Food System</span>
                    </div>
                </a>

                <!-- ── Desktop Nav Tabs ── -->
                <nav class="hidden lg:flex items-center gap-1 flex-1 justify-center">
                    <?php if ($userRole === 'admin' || $userRole === 'caja'): ?>
                    <!-- POS Venta – Primary CTA -->
                    <a href="<?= BASE_URL ?>/pos"
                       class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all duration-200 flex items-center gap-2 shadow-lg shadow-accent/20 active:scale-95 bg-gradient-to-r from-accent to-accent-dark text-white hover:brightness-110 border border-white/15 mr-1">
                        <!-- Cart icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                        </svg>
                        <span>POS Venta</span>
                    </a>
                    <?php endif; ?>

                    <?php if ($userRole === 'admin' || $userRole === 'caja' || $userRole === 'cocinero'): ?>
                    <!-- Pedidos / Cocina – Secondary CTA -->
                    <a href="<?= BASE_URL ?>/orders"
                       class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all duration-200 flex items-center gap-2 shadow-lg shadow-blue-500/20 active:scale-95 bg-gradient-to-r from-blue-500 to-indigo-600 text-white hover:brightness-110 border border-white/15 mr-1">
                        <!-- Bell/Orders icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                        </svg>
                        <span>Pedidos / Cocina</span>
                    </a>
                    <?php endif; ?>

                    <?php
                    // ─── 5 NAV TAB GROUPS ─────────────────────────────────────────────
                    $navGroups = [];

                    // 1. ADMINISTRACIÓN
                    if ($userRole === 'admin') {
                        $navGroups[] = [
                            'id'    => 'admin',
                            'title' => 'Administración',
                            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>',
                            'items' => [
                                ['/','<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>','Dashboard','Resumen general de ventas'],
                                ['/users','<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>','Usuarios','Control de personal y permisos'],
                                ['/settings','<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>','Configuración','Ajustes del sistema'],
                            ]
                        ];
                    }

                    // 2. PRODUCTOS
                    if ($userRole === 'admin') {
                        $navGroups[] = [
                            'id'    => 'products',
                            'title' => 'Productos',
                            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>',
                            'items' => [
                                ['/products','<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>','Gestión Productos','Catálogo, precios y menú'],
                                ['/sales/price-history','<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>','Precios Históricos','Historial y vigencia de precios'],
                                ['/categories','<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>','Categorías','Organización del menú'],
                                ['/raw-materials','<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>','Control Insumos','Inventario y materias primas'],
                            ]
                        ];
                    }

                    // 3. GESTIÓN
                    if ($userRole === 'admin') {
                        $navGroups[] = [
                            'id'    => 'gestion',
                            'title' => 'Gestión',
                            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>',
                            'items' => [
                                ['/admin-expenses','<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>','Gastos Admin','Egresos y operaciones'],
                            ]
                        ];
                    }

                    // 4. VENTAS Y COMPRAS
                    $salesItems = [];
                    if ($userRole === 'admin' || $userRole === 'caja') {
                        $salesItems[] = ['/sales/history','<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>','Historial Ventas','Consulta de transacciones'];
                        $salesItems[] = ['/sales/create-manual','<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>','Pedido Histórico','Registro de ventas pasadas'];
                        $salesItems[] = ['/sales/price-history','<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>','Precios Históricos','Historial de precios por producto'];
                    }
                    if (!empty($salesItems)) {
                        $navGroups[] = [
                            'id'    => 'ventas',
                            'title' => 'Ventas',
                            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>',
                            'items' => $salesItems
                        ];
                    }

                    // 5. REPORTES
                    if ($userRole === 'admin') {
                        $navGroups[] = [
                            'id'    => 'reportes',
                            'title' => 'Reportes',
                            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>',
                            'items' => [
                                ['/reports','<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>','Informe General','Estadísticas y balance'],
                            ]
                        ];
                    }
                    ?>

                    <?php foreach ($navGroups as $gKey => $group):
                        $hasActive = false;
                        foreach ($group['items'] as [$path]) {
                            if (($path === '/' && ($currentUri === BASE_URL . '/' || $currentUri === BASE_URL)) ||
                                ($path !== '/' && strpos($currentUri, BASE_URL . $path) === 0)) {
                                $hasActive = true;
                                break;
                            }
                        }
                        $btnId   = 'nav-btn-'  . $group['id'];
                        $panelId = 'nav-panel-' . $group['id'];
                    ?>
                    <!-- Desktop Dropdown Tab: <?= $group['title'] ?> -->
                    <div class="relative" style="display:inline-block;">
                        <button id="<?= $btnId ?>" type="button"
                                onclick="toggleNavDropdown('<?= $panelId ?>', '<?= $btnId ?>', event)"
                                class="nav-dropdown-btn px-3 py-2 rounded-xl text-xs font-semibold flex items-center gap-1.5 border focus:outline-none focus-visible:ring-2 focus-visible:ring-accent/50
                                    <?= $hasActive
                                        ? 'bg-white/15 text-white font-bold border-white/25 shadow-inner'
                                        : 'bg-white/5 text-white/70 hover:bg-white/10 hover:text-white border-white/10 hover:border-white/20' ?>">
                            <span class="[&_svg]:w-3.5 [&_svg]:h-3.5 opacity-80"><?= $group['icon'] ?></span>
                            <span class="tracking-tight"><?= $group['title'] ?></span>
                            <?php if ($hasActive): ?>
                            <span class="w-1.5 h-1.5 rounded-full bg-accent"></span>
                            <?php endif; ?>
                            <svg id="<?= $btnId ?>-chev" class="w-3 h-3 opacity-50 nav-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Dropdown Panel (appended to body via JS) -->
                    <div id="<?= $panelId ?>" class="nav-dropdown-panel" data-btn="<?= $btnId ?>">
                        <div class="px-3 py-1.5 mb-1 border-b border-white/8">
                            <span class="text-[9px] font-bold uppercase tracking-widest text-white/35"><?= $group['title'] ?></span>
                        </div>
                        <div class="space-y-0.5">
                            <?php foreach ($group['items'] as [$path, $icon, $label, $desc]):
                                $isActive = ($path === '/')
                                    ? ($currentUri === BASE_URL . '/' || $currentUri === BASE_URL)
                                    : (strpos($currentUri, BASE_URL . $path) === 0);
                            ?>
                            <a href="<?= BASE_URL . $path ?>"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-xs transition-all duration-100 group/item
                                      <?= $isActive
                                            ? 'bg-gradient-to-r from-accent/30 to-accent/10 text-white font-bold border border-accent/25'
                                            : 'text-white/75 hover:bg-white/12 hover:text-white border border-transparent' ?>">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 border [&_svg]:w-3.5 [&_svg]:h-3.5
                                            <?= $isActive ? 'bg-accent/20 border-accent/30 text-white' : 'bg-white/8 border-white/12 text-white/70 group-hover/item:border-white/20 group-hover/item:text-white' ?>">
                                    <?= $icon ?>
                                </div>
                                <div class="flex flex-col flex-grow min-w-0">
                                    <span class="text-xs font-semibold leading-tight"><?= $label ?></span>
                                    <span class="text-[10px] text-white/40 group-hover/item:text-white/60 transition-colors"><?= $desc ?></span>
                                </div>
                                <?php if ($isActive): ?>
                                <span class="w-1.5 h-1.5 rounded-full bg-accent flex-shrink-0"></span>
                                <?php endif; ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </nav>

                <!-- ── Right: User + Hamburger ── -->
                <div class="flex items-center gap-2.5 shrink-0">
                    <!-- Desktop User Badge -->
                    <div class="hidden lg:flex items-center gap-2.5 pl-3 border-l border-white/10">
                        <div class="flex flex-col text-right">
                            <span class="text-xs font-semibold text-white/90 leading-tight"><?= e($currentUser['full_name']) ?></span>
                            <span class="text-[9px] text-amber-300 font-bold uppercase tracking-wider bg-amber-400/10 px-1.5 py-0.5 rounded-md border border-amber-400/20 w-max ml-auto mt-0.5"><?= e($userRole) ?></span>
                        </div>
                        <a href="<?= BASE_URL ?>/logout" title="Cerrar sesión"
                           class="w-8 h-8 flex items-center justify-center rounded-xl bg-red-500/10 hover:bg-red-600 text-red-300 hover:text-white border border-red-500/20 hover:border-red-600 transition-all duration-200 active:scale-95 focus:outline-none focus:ring-2 focus:ring-red-500 group">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                            </svg>
                        </a>
                    </div>

                    <!-- Mobile Hamburger -->
                    <button id="mobile-menu-btn" type="button" aria-label="Abrir menú"
                            class="lg:hidden w-9 h-9 rounded-xl bg-white/5 hover:bg-white/12 active:bg-white/20 border border-white/10 flex items-center justify-center transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-accent">
                        <div class="w-4.5 h-3.5 relative flex flex-col justify-between items-center">
                            <span id="ham-line-1" class="w-[18px] h-0.5 bg-white rounded-full transition-all duration-300 origin-center"></span>
                            <span id="ham-line-2" class="w-[18px] h-0.5 bg-white rounded-full transition-all duration-300 origin-center"></span>
                            <span id="ham-line-3" class="w-[18px] h-0.5 bg-white rounded-full transition-all duration-300 origin-center"></span>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Backdrop -->
        <div id="mobile-backdrop" class="fixed inset-0 z-40 bg-black/70 backdrop-blur-sm hidden lg:hidden" onclick="closeMobileMenu()"></div>

        <!-- ═══════════════════════════════════════════════════════════
             MOBILE DRAWER MENU
        ═══════════════════════════════════════════════════════════ -->
        <div id="mobile-menu" class="fixed top-16 left-0 right-0 z-50 lg:hidden max-h-[calc(100vh-4rem)] overflow-y-auto custom-scrollbar
                -translate-y-3 opacity-0 pointer-events-none transition-all duration-300 ease-out">
            <div class="mx-3 my-2 rounded-2xl overflow-hidden shadow-2xl border border-white/12"
                 style="background: linear-gradient(180deg, #1e0d01 0%, #2e1402 100%);">

                <!-- Mobile User Header -->
                <div class="px-4 py-3.5 bg-white/5 border-b border-white/10 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-accent/20 border border-accent/30 flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-accent" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-white leading-tight"><?= e($currentUser['full_name']) ?></div>
                            <div class="text-[10px] text-white/50">@<?= e($currentUser['username']) ?></div>
                        </div>
                    </div>
                    <span class="text-[10px] text-amber-300 font-bold uppercase tracking-wider bg-amber-400/15 px-2 py-1 rounded-lg border border-amber-400/25"><?= e($userRole) ?></span>
                </div>

                <!-- Mobile POS Button -->
                <?php if ($userRole === 'admin' || $userRole === 'caja'): ?>
                <div class="p-3 pb-0">
                    <a href="<?= BASE_URL ?>/pos" onclick="closeMobileMenu()"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl bg-gradient-to-r from-accent to-accent-dark text-white font-bold text-sm shadow-lg border border-white/15 active:scale-[0.98] transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                        </svg>
                        <span>POS Venta</span>
                    </a>
                </div>
                <?php endif; ?>

                <!-- Mobile Pedidos / Cocina Button -->
                <?php if ($userRole === 'admin' || $userRole === 'caja' || $userRole === 'cocinero'): ?>
                <div class="p-3 pb-0 <?php echo ($userRole === 'admin' || $userRole === 'caja') ? 'pt-2' : ''; ?>">
                    <a href="<?= BASE_URL ?>/orders" onclick="closeMobileMenu()"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-bold text-sm shadow-lg shadow-blue-500/20 border border-white/15 active:scale-[0.98] transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                        </svg>
                        <span>Pedidos / Cocina</span>
                    </a>
                </div>
                <?php endif; ?>

                <!-- Mobile Accordion Groups -->
                <div class="p-3 space-y-1.5">
                    <?php
                    // Re-use the same navGroups from desktop
                    foreach ($navGroups as $gIdx => $group):
                        $groupHasActive = false;
                        foreach ($group['items'] as [$path]) {
                            if (($path === '/' && ($currentUri === BASE_URL . '/' || $currentUri === BASE_URL)) ||
                                ($path !== '/' && strpos($currentUri, BASE_URL . $path) === 0)) {
                                $groupHasActive = true;
                                break;
                            }
                        }
                        $accId = 'mob-acc-' . $group['id'];
                    ?>
                    <div class="rounded-xl overflow-hidden border <?= $groupHasActive ? 'border-accent/25' : 'border-white/8' ?>">
                        <!-- Accordion Header -->
                        <button type="button" onclick="toggleMobileAcc('<?= $accId ?>', this)"
                                class="w-full flex items-center justify-between gap-3 px-3.5 py-3 text-left transition-colors <?= $groupHasActive ? 'bg-accent/15 text-white' : 'bg-white/5 text-white/75 hover:bg-white/10 hover:text-white' ?>">
                            <div class="flex items-center gap-2.5">
                                <span class="[&_svg]:w-4 [&_svg]:h-4 opacity-75"><?= $group['icon'] ?></span>
                                <span class="text-sm font-semibold"><?= $group['title'] ?></span>
                                <?php if ($groupHasActive): ?>
                                <span class="w-1.5 h-1.5 rounded-full bg-accent"></span>
                                <?php endif; ?>
                            </div>
                            <svg class="w-4 h-4 opacity-50 mobile-acc-chevron <?= $groupHasActive ? 'open' : '' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <!-- Accordion Body -->
                        <div id="<?= $accId ?>" class="mobile-acc-body <?= $groupHasActive ? 'open' : '' ?> bg-black/20">
                            <div class="p-2 space-y-0.5">
                                <?php foreach ($group['items'] as [$path, $icon, $label, $desc]):
                                    $isActive = ($path === '/')
                                        ? ($currentUri === BASE_URL . '/' || $currentUri === BASE_URL)
                                        : (strpos($currentUri, BASE_URL . $path) === 0);
                                ?>
                                <a href="<?= BASE_URL . $path ?>" onclick="closeMobileMenu()"
                                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition-all active:scale-[0.98]
                                          <?= $isActive
                                                ? 'bg-accent/20 text-white font-bold border border-accent/25'
                                                : 'text-white/70 hover:bg-white/10 hover:text-white border border-transparent' ?>">
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 [&_svg]:w-3.5 [&_svg]:h-3.5
                                                <?= $isActive ? 'bg-accent/25 border border-accent/30' : 'bg-white/8 border border-white/10' ?>">
                                        <?= $icon ?>
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <span class="text-xs font-semibold leading-tight"><?= $label ?></span>
                                        <span class="text-[10px] text-white/40 truncate"><?= $desc ?></span>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Mobile Logout -->
                <div class="p-3 border-t border-white/10">
                    <a href="<?= BASE_URL ?>/logout"
                       class="w-full text-sm bg-red-600/15 hover:bg-red-600 border border-red-500/25 text-red-300 hover:text-white px-4 py-2.5 rounded-xl font-bold transition-all duration-200 flex items-center justify-center gap-2 active:scale-[0.98]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                        <span>Cerrar Sesión</span>
                    </a>
                </div>
            </div>
        </div>
    </header>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="flex-grow max-w-[70%] w-full mx-auto p-4 sm:p-6 lg:p-8">
        
        <!-- Flash Messages (now rendered as toasts via JS) -->
        <?php if ($success = Auth::getFlash('success')): ?>
        <script>document.addEventListener('DOMContentLoaded',()=>showToast(<?= json_encode($success) ?>,'success'));</script>
        <?php endif; ?>
        <?php if ($error = Auth::getFlash('error')): ?>
        <script>document.addEventListener('DOMContentLoaded',()=>showToast(<?= json_encode($error) ?>,'error'));</script>
        <?php endif; ?>

        <!-- Child View Content -->
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="bg-coffee-dark/5 border-t border-coffee-dark/10 py-5 text-center text-xs text-coffee-medium">
        <p>&copy; <?= date('Y') ?> Duke's Fast Food POS. Todos los derechos reservados.</p>
    </footer>

    <!-- Global JS -->
    <script>
        (function () {
            const btn   = document.getElementById('mobile-menu-btn');
            const menu  = document.getElementById('mobile-menu');
            const back  = document.getElementById('mobile-backdrop');
            const l1    = document.getElementById('ham-line-1');
            const l2    = document.getElementById('ham-line-2');
            const l3    = document.getElementById('ham-line-3');
            let isOpen  = false;

            window.closeMobileMenu = function () {
                if (!isOpen) return;
                isOpen = false;
                menu.classList.remove('translate-y-0', 'opacity-100', 'pointer-events-auto');
                menu.classList.add('-translate-y-3', 'opacity-0', 'pointer-events-none');
                back.classList.add('hidden');
                if (l1) { l1.style.transform = ''; }
                if (l2) { l2.style.transform = ''; l2.style.opacity = '1'; }
                if (l3) { l3.style.transform = ''; }
            };

            function openMenu() {
                isOpen = true;
                back.classList.remove('hidden');
                menu.classList.remove('-translate-y-3', 'opacity-0', 'pointer-events-none');
                menu.classList.add('translate-y-0', 'opacity-100', 'pointer-events-auto');
                if (l1) l1.style.transform = 'translateY(7px) rotate(45deg)';
                if (l2) { l2.style.transform = 'scaleX(0)'; l2.style.opacity = '0'; }
                if (l3) l3.style.transform = 'translateY(-7px) rotate(-45deg)';
            }

            if (btn) btn.addEventListener('click', () => isOpen ? closeMobileMenu() : openMenu());
        })();

        // Mobile Accordion Toggle
        window.toggleMobileAcc = function(id, btn) {
            const body    = document.getElementById(id);
            const chevron = btn.querySelector('.mobile-acc-chevron');
            const isOpen  = body.classList.contains('open');
            if (isOpen) {
                body.classList.remove('open');
                chevron.classList.remove('open');
            } else {
                body.classList.add('open');
                chevron.classList.add('open');
            }
        };

        // ── Desktop Nav Dropdowns (click-based, position:fixed) ──────────
        (function() {
            let currentPanel = null;
            let currentBtnId = null;

            // Move all panels to <body> so they escape any overflow clipping
            document.querySelectorAll('.nav-dropdown-panel').forEach(panel => {
                document.body.appendChild(panel);
            });

            function positionPanel(panel, btnEl) {
                const rect = btnEl.getBoundingClientRect();
                panel.style.left = Math.min(rect.left, window.innerWidth - 260) + 'px';
                // panel is position:fixed, top = header bottom (64px)
                panel.style.top  = '68px';
            }

            function closeAll(exceptPanel) {
                document.querySelectorAll('.nav-dropdown-panel.open').forEach(p => {
                    if (p !== exceptPanel) {
                        p.classList.remove('open');
                        const bId = p.getAttribute('data-btn');
                        const chev = document.getElementById(bId + '-chev');
                        if (chev) chev.classList.remove('rotated');
                    }
                });
            }

            window.toggleNavDropdown = function(panelId, btnId, e) {
                e.stopPropagation();
                const panel = document.getElementById(panelId);
                const btn   = document.getElementById(btnId);
                const chev  = document.getElementById(btnId + '-chev');
                if (!panel || !btn) return;

                const isOpen = panel.classList.contains('open');

                // Close all first
                closeAll(null);

                if (!isOpen) {
                    positionPanel(panel, btn);
                    panel.classList.add('open');
                    if (chev) chev.classList.add('rotated');
                    currentPanel = panel;
                    currentBtnId = btnId;
                } else {
                    if (chev) chev.classList.remove('rotated');
                    currentPanel = null;
                    currentBtnId = null;
                }
            };

            // Close on outside click
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.nav-dropdown-panel') &&
                    !e.target.closest('[id^="nav-btn-"]')) {
                    closeAll(null);
                    currentPanel = null;
                    currentBtnId = null;
                }
            });

            // Reposition on scroll/resize
            window.addEventListener('scroll', function() {
                if (currentPanel && currentBtnId) {
                    const btn = document.getElementById(currentBtnId);
                    if (btn) positionPanel(currentPanel, btn);
                }
            }, { passive: true });

            window.addEventListener('resize', function() {
                closeAll(null);
                currentPanel = null;
            });
        })();
    </script>

    <!-- ═══════════════════════════════════════════════
         GLOBAL TOAST NOTIFICATION SYSTEM
    ═══════════════════════════════════════════════ -->
    <div id="toast-container"
         style="position:fixed;top:76px;right:20px;z-index:99999;display:flex;flex-direction:column;gap:10px;pointer-events:none;width:340px;max-width:calc(100vw - 32px);"
         aria-live="polite" aria-atomic="false">
    </div>

    <style>
        @keyframes toastIn {
            from { opacity:0; transform: translateX(110%) scale(0.95); }
            to   { opacity:1; transform: translateX(0)   scale(1); }
        }
        @keyframes toastOut {
            from { opacity:1; transform: translateX(0)   scale(1);    max-height:120px; margin-bottom:0; }
            to   { opacity:0; transform: translateX(110%) scale(0.94); max-height:0;    margin-bottom:-10px; }
        }
        @keyframes toastProgress {
            from { width: 100%; }
            to   { width: 0%; }
        }
        .toast-item {
            animation: toastIn 0.38s cubic-bezier(0.16,1,0.3,1) both;
            pointer-events: auto;
        }
        .toast-item.removing {
            animation: toastOut 0.3s cubic-bezier(0.7,0,0.84,0) forwards;
        }
        .toast-progress-bar {
            animation: toastProgress linear forwards;
        }
    </style>

    <script>
    (function() {
        const TOAST_VARIANTS = {
            success: {
                bg:     'linear-gradient(135deg, #064e3b 0%, #065f46 100%)',
                border: 'rgba(52,211,153,0.35)',
                icon:   '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#34d399" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
                accent: '#34d399',
                label:  '✅ Éxito',
            },
            error: {
                bg:     'linear-gradient(135deg, #450a0a 0%, #7f1d1d 100%)',
                border: 'rgba(248,113,113,0.35)',
                icon:   '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
                accent: '#f87171',
                label:  '❌ Error',
            },
            warning: {
                bg:     'linear-gradient(135deg, #451a03 0%, #78350f 100%)',
                border: 'rgba(251,191,36,0.35)',
                icon:   '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
                accent: '#fbbf24',
                label:  '⚠️ Atención',
            },
            info: {
                bg:     'linear-gradient(135deg, #0c1a4a 0%, #1e3a8a 100%)',
                border: 'rgba(96,165,250,0.35)',
                icon:   '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>',
                accent: '#60a5fa',
                label:  'ℹ️ Info',
            },
        };

        window.showToast = function(message, type = 'info', duration = 5000) {
            const v = TOAST_VARIANTS[type] || TOAST_VARIANTS.info;
            const container = document.getElementById('toast-container');
            if (!container) return;

            const id = 'toast-' + Date.now() + '-' + Math.random().toString(36).slice(2);

            const el = document.createElement('div');
            el.id = id;
            el.className = 'toast-item';
            el.style.cssText = `
                background: ${v.bg};
                border: 1px solid ${v.border};
                border-radius: 16px;
                padding: 14px 14px 12px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.55), 0 0 0 1px rgba(255,255,255,0.06);
                display: flex;
                flex-direction: column;
                gap: 8px;
                overflow: hidden;
                position: relative;
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
            `;

            el.innerHTML = `
                <div style="display:flex;align-items:flex-start;gap:11px;">
                    <div style="flex-shrink:0;width:36px;height:36px;border-radius:10px;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.12);display:flex;align-items:center;justify-content:center;">
                        ${v.icon}
                    </div>
                    <div style="flex:1;min-width:0;">
                        <p style="font-size:11px;font-weight:800;color:${v.accent};text-transform:uppercase;letter-spacing:0.08em;margin-bottom:2px;font-family:'Outfit',sans-serif;">${v.label}</p>
                        <p style="font-size:13px;font-weight:600;color:rgba(255,255,255,0.92);line-height:1.45;word-break:break-word;">${message}</p>
                    </div>
                    <button onclick="removeToast('${id}')" style="flex-shrink:0;width:26px;height:26px;border-radius:8px;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.12);color:rgba(255,255,255,0.55);font-size:16px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .15s;line-height:1;" onmouseover="this.style.background='rgba(255,255,255,0.18)'" onmouseout="this.style.background='rgba(255,255,255,0.08)'">&times;</button>
                </div>
                <div style="height:3px;background:rgba(255,255,255,0.10);border-radius:999px;overflow:hidden;">
                    <div class="toast-progress-bar" style="height:100%;background:${v.accent};border-radius:999px;animation-duration:${duration}ms;"></div>
                </div>
            `;

            container.appendChild(el);

            const timer = setTimeout(() => removeToast(id), duration);
            el._toastTimer = timer;
        };

        window.removeToast = function(id) {
            const el = document.getElementById(id);
            if (!el) return;
            if (el._toastTimer) clearTimeout(el._toastTimer);
            el.classList.add('removing');
            setTimeout(() => el.remove(), 320);
        };
    })();
    </script>
</body>
</html>
