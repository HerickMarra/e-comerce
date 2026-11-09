<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Woocommerce Admin') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />

    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://unpkg.com/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                screens: {
                    'xs': '375px',
                    'sm': '640px',
                    'md': '768px',
                    'lg': '1024px',
                    'xl': '1280px',
                    '2xl': '1536px',
                },
                extend: {
                    colors: {
                        "primary": "{{ $appSettings['primary_color'] ?? '#10b981' }}",
                        "background-light": "#fcfcfc",
                        "background-dark": "{{ $appSettings['secondary_color'] ?? '#0f172a' }}",
                        "sidebar-dark": "{{ $appSettings['secondary_color'] ?? '#1e293b' }}",
                    },
                    fontFamily: {
                        "sans": ["Inter", "sans-serif"]
                    }
                },
            },
        }
    </script>
    <style type="text/tailwindcss">
        @layer base {
            body { font-family: 'Inter', sans-serif; }
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        [x-cloak] { display: none !important; }
        @layer utilities {
            .hover-primary:hover {
                background-color: theme('colors.primary');
                filter: brightness(0.9);
            }
            .bg-primary {
                transition: all 0.2s;
            }
            .bg-primary:hover {
                filter: brightness(0.9);
            }
        }
        /* Custom scrollbar for mobile drawers & tables */
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.3);
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(148, 163, 184, 0.5);
        }
    </style>

    <script>
        function adminNav() {
            return {
                sidebarOpen: window.innerWidth >= 1024,
                searchMobileOpen: false,
                isMobile() {
                    return window.innerWidth < 1024;
                },
                toggleSidebar() {
                    this.sidebarOpen = !this.sidebarOpen;
                },
                closeSidebarOnMobile() {
                    if (this.isMobile()) {
                        this.sidebarOpen = false;
                    }
                },
                init() {
                    const handleResize = () => {
                        if (window.innerWidth >= 1024) {
                            this.sidebarOpen = true;
                        } else if (this.sidebarOpen && !this._userOpenedMobile) {
                            this.sidebarOpen = false;
                        }
                    };
                    window.addEventListener('resize', handleResize);
                }
            };
        }
    </script>
</head>

<body class="bg-slate-50 dark:bg-background-dark text-slate-900 dark:text-slate-100 antialiased selection:bg-primary/20"
    x-data="adminNav()"
    :class="{ 'overflow-hidden': sidebarOpen && isMobile() }"
    @keydown.escape.window="if(isMobile()) sidebarOpen = false">

    <div class="flex h-screen overflow-hidden relative">

        <!-- Mobile Drawer Backdrop Overlay -->
        <div x-show="sidebarOpen && isMobile()"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-slate-950/70 backdrop-blur-sm lg:hidden"
            x-cloak>
        </div>

        <!-- Sidebar Drawer (mobile) & Fixed Sidebar (desktop) -->
        <aside
            class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] bg-sidebar-dark text-slate-300 transition-transform duration-300 ease-in-out transform lg:translate-x-0 lg:static lg:inset-0 shrink-0 flex flex-col shadow-2xl lg:shadow-none"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            x-cloak>
            
            <div class="flex flex-col h-full min-h-0">
                <!-- Sidebar Header -->
                <div class="flex items-center justify-between h-16 sm:h-20 px-5 sm:px-6 border-b border-slate-700/50 shrink-0">
                    <a href="{{ route('admin.dashboard') }}" @click="closeSidebarOnMobile()" class="flex items-center gap-2.5 group min-w-0">
                        @if($appSettings['store_logo'])
                            <img src="{{ asset('storage/' . $appSettings['store_logo']) }}"
                                class="w-auto object-contain max-w-[140px] sm:max-w-[160px]"
                                style="height: {{ ($appSettings['store_logo_size'] ?? 100) * 0.3 }}px; max-height: 52px;"
                                alt="{{ $appSettings['store_name'] }}">
                        @else
                            <div
                                class="w-8 h-8 bg-primary rounded-xl flex items-center justify-center text-white group-hover:rotate-6 transition-transform shadow-md shadow-primary/20 shrink-0">
                                <span
                                    class="material-symbols-outlined text-xl">{{ $appSettings['store_icon'] ?? 'steps' }}</span>
                            </div>
                            <span
                                class="text-base sm:text-lg font-black tracking-tight text-white uppercase truncate">{{ $appSettings['store_name'] ?? 'Denner Tênis' }}</span>
                        @endif
                    </a>

                    <!-- Close Button for Mobile Drawer -->
                    <button type="button" @click="sidebarOpen = false"
                        class="lg:hidden size-9 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/80 flex items-center justify-center transition-colors"
                        aria-label="Fechar menu lateral">
                        <span class="material-symbols-outlined text-xl">close</span>
                    </button>
                </div>

                <!-- Navigation List -->
                <nav class="flex-grow px-3 sm:px-4 py-5 space-y-1.5 overflow-y-auto custom-scrollbar">
                    <p class="px-3 text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2.5">Módulos Principais</p>

                    <a href="{{ route('admin.dashboard') }}" @click="closeSidebarOnMobile()"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-primary text-white shadow-lg shadow-primary/25 font-bold' : 'hover:bg-slate-800/60 hover:text-white font-medium' }}">
                        <span class="material-symbols-outlined text-xl shrink-0">dashboard</span>
                        <span class="text-sm">Dashboard</span>
                    </a>

                    <a href="{{ route('admin.products.index') }}" @click="closeSidebarOnMobile()"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.products.*') ? 'bg-primary text-white shadow-lg shadow-primary/25 font-bold' : 'hover:bg-slate-800/60 hover:text-white font-medium' }}">
                        <span class="material-symbols-outlined text-xl shrink-0">inventory_2</span>
                        <span class="text-sm">Produtos</span>
                    </a>

                    <a href="{{ route('admin.categories.index') }}" @click="closeSidebarOnMobile()"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.categories.*') ? 'bg-primary text-white shadow-lg shadow-primary/25 font-bold' : 'hover:bg-slate-800/60 hover:text-white font-medium' }}">
                        <span class="material-symbols-outlined text-xl shrink-0">category</span>
                        <span class="text-sm">Categorias</span>
                    </a>

                    <a href="{{ route('admin.orders.index') }}" @click="closeSidebarOnMobile()"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.orders.*') ? 'bg-primary text-white shadow-lg shadow-primary/25 font-bold' : 'hover:bg-slate-800/60 hover:text-white font-medium' }}">
                        <span class="material-symbols-outlined text-xl shrink-0">shopping_cart</span>
                        <span class="text-sm">Pedidos</span>
                    </a>

                    <a href="{{ route('admin.banners.index') }}" @click="closeSidebarOnMobile()"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.banners.*') ? 'bg-primary text-white shadow-lg shadow-primary/25 font-bold' : 'hover:bg-slate-800/60 hover:text-white font-medium' }}">
                        <span class="material-symbols-outlined text-xl shrink-0">view_carousel</span>
                        <span class="text-sm">Banners & Vídeos</span>
                    </a>

                    <div class="pt-5">
                        <p class="px-3 text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2.5">Configurações</p>
                        
                        <a href="{{ route('admin.users.index') }}" @click="closeSidebarOnMobile()"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.users.*') ? 'bg-primary text-white shadow-lg shadow-primary/25 font-bold' : 'hover:bg-slate-800/60 hover:text-white font-medium' }}">
                            <span class="material-symbols-outlined text-xl shrink-0">group</span>
                            <span class="text-sm">Usuários</span>
                        </a>

                        <a href="{{ route('admin.emails.index') }}" @click="closeSidebarOnMobile()"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.emails.*') ? 'bg-primary text-white shadow-lg shadow-primary/25 font-bold' : 'hover:bg-slate-800/60 hover:text-white font-medium' }}">
                            <span class="material-symbols-outlined text-xl shrink-0">mark_email_read</span>
                            <span class="text-sm">Modelos de E-mail</span>
                        </a>

                        <a href="{{ route('admin.newsletters.index') }}" @click="closeSidebarOnMobile()"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.newsletters.*') ? 'bg-primary text-white shadow-lg shadow-primary/25 font-bold' : 'hover:bg-slate-800/60 hover:text-white font-medium' }}">
                            <span class="material-symbols-outlined text-xl shrink-0">mail</span>
                            <span class="text-sm">Newsletter</span>
                        </a>

                        <a href="{{ route('admin.appearance.index') }}" @click="closeSidebarOnMobile()"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.appearance.*') ? 'bg-primary text-white shadow-lg shadow-primary/25 font-bold' : 'hover:bg-slate-800/60 hover:text-white font-medium' }}">
                            <span class="material-symbols-outlined text-xl shrink-0">web</span>
                            <span class="text-sm">Aparência</span>
                        </a>

                        <a href="{{ route('admin.settings.index') }}" @click="closeSidebarOnMobile()"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.settings.*') ? 'bg-primary text-white shadow-lg shadow-primary/25 font-bold' : 'hover:bg-slate-800/60 hover:text-white font-medium' }}">
                            <span class="material-symbols-outlined text-xl shrink-0">settings</span>
                            <span class="text-sm">Configurações Gerais</span>
                        </a>
                    </div>
                </nav>

                <!-- Sidebar User Footer -->
                <div class="p-3.5 sm:p-4 bg-slate-800/60 border-t border-slate-700/50 shrink-0">
                    <div class="flex items-center gap-3 px-1">
                        <div
                            class="size-9 rounded-xl bg-primary text-white flex items-center justify-center font-black text-sm shrink-0 shadow-md">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="flex-grow min-w-0">
                            <p class="text-xs sm:text-sm font-bold text-white truncate">{{ Auth::user()->name ?? 'Administrador' }}</p>
                            <p class="text-[11px] text-slate-400 truncate">
                                {{ Auth::user()->roles->first()->name ?? 'Admin' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <main class="flex-grow min-w-0 relative overflow-y-auto focus:outline-none custom-scrollbar flex flex-col">
            <!-- Top Header -->
            <header
                class="h-16 sm:h-20 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800 sticky top-0 z-30 px-3.5 sm:px-6 lg:px-8 shrink-0">
                <div class="flex items-center justify-between h-full gap-2 sm:gap-4">
                    <!-- Left: Hamburger Toggle & Page Title -->
                    <div class="flex items-center gap-2.5 sm:gap-4 min-w-0">
                        <button type="button" @click="toggleSidebar()"
                            class="size-10 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 flex items-center justify-center transition-colors shrink-0"
                            aria-label="Abrir ou fechar menu lateral">
                            <span class="material-symbols-outlined text-2xl">menu</span>
                        </button>
                        
                        <div class="min-w-0">
                            <h1 class="text-sm xs:text-base sm:text-lg font-black text-slate-900 dark:text-white truncate max-w-[130px] xs:max-w-[190px] sm:max-w-xs md:max-w-md lg:max-w-xl">
                                @yield('page_title', 'Painel Administrativo')
                            </h1>
                        </div>
                    </div>

                    <!-- Right: Search, Notifications & Actions -->
                    <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">
                        <!-- Desktop Search -->
                        <div
                            class="hidden md:flex items-center bg-slate-100 dark:bg-slate-800 px-3 py-2 rounded-xl border border-transparent focus-within:border-primary/50 transition-all">
                            <span class="material-symbols-outlined text-slate-400 text-lg">search</span>
                            <input type="text" placeholder="Pesquisar..."
                                class="bg-transparent border-none focus:ring-0 text-xs sm:text-sm w-36 lg:w-48 text-slate-600 dark:text-slate-300">
                        </div>

                        <!-- Mobile Search Toggle Button -->
                        <button type="button" @click="searchMobileOpen = !searchMobileOpen"
                            class="md:hidden size-9 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center transition-colors"
                            aria-label="Abrir pesquisa">
                            <span class="material-symbols-outlined text-xl">search</span>
                        </button>

                        <!-- Notification Pill -->
                        <button type="button"
                            class="relative size-9 sm:size-10 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center transition-colors"
                            aria-label="Notificações">
                            <span class="material-symbols-outlined text-xl">notifications</span>
                            <span class="absolute top-2 right-2 size-2 bg-red-500 rounded-full ring-2 ring-white dark:ring-slate-900"></span>
                        </button>

                        <div class="w-px h-5 bg-slate-200 dark:bg-slate-800 mx-0.5 sm:mx-1 hidden xs:block"></div>

                        <!-- Logout Button -->
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 rounded-xl hover:bg-red-50 dark:hover:bg-red-950/40 text-red-600 transition-all group font-bold text-xs"
                                title="Encerrar Sessão">
                                <span class="material-symbols-outlined text-lg group-hover:rotate-12 transition-transform">logout</span>
                                <span class="hidden sm:inline uppercase tracking-wider text-[11px]">Sair</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Mobile Collapsible Search Bar -->
                <div x-show="searchMobileOpen" x-cloak
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                    class="md:hidden pb-3 pt-1">
                    <div class="flex items-center bg-slate-100 dark:bg-slate-800 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700">
                        <span class="material-symbols-outlined text-slate-400 text-lg mr-2">search</span>
                        <input type="text" placeholder="Buscar no painel..."
                            class="bg-transparent border-none focus:ring-0 text-xs w-full text-slate-700 dark:text-slate-200 p-0">
                        <button type="button" @click="searchMobileOpen = false" class="text-slate-400 hover:text-slate-600 p-1">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Main Content Container with Mobile-First Spacing -->
            <div class="p-3.5 sm:p-6 lg:p-8 flex-1 w-full max-w-full overflow-x-hidden">
                {{ $slot }}
            </div>
        </main>
    </div>
    @stack('scripts')
</body>

</html>
