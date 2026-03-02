<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Woocommerce Admin') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />

    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
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
    </style>
</head>

<body class="bg-slate-50 dark:bg-background-dark text-slate-900 dark:text-slate-100 antialiased"
    x-data="{ sidebarOpen: true }">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-50 w-64 bg-sidebar-dark text-slate-300 transition-transform duration-300 transform lg:translate-x-0 lg:static lg:inset-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:hidden'">
            <div class="flex flex-col h-full">
                <!-- Sidebar Header -->
                <div class="flex items-center justify-between h-20 px-6 border-b border-slate-700/50">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 group">
                        @if($appSettings['store_logo'])
                            <img src="{{ asset('storage/' . $appSettings['store_logo']) }}"
                                class="w-auto object-contain max-w-[150px]"
                                style="height: {{ ($appSettings['store_logo_size'] ?? 100) * 0.3 }}px; max-height: 48px;"
                                alt="{{ $appSettings['store_name'] }}">
                        @else
                            <div
                                class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center text-white group-hover:rotate-6 transition-transform">
                                <span
                                    class="material-symbols-outlined text-xl">{{ $appSettings['store_icon'] ?? 'chair' }}</span>
                            </div>
                            <span
                                class="text-xl font-bold tracking-tight text-white uppercase">{{ $appSettings['store_name'] ?? 'Admin' }}</span>
                        @endif
                    </a>
                </div>

                <!-- Navigation -->
                <nav class="flex-grow px-4 py-6 space-y-2 overflow-y-auto custom-scrollbar">
                    <p class="px-2 text-xs font-bold text-slate-500 uppercase tracking-widest mb-4">Módulos Principais
                    </p>

                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-slate-700/50 hover:text-white' }}">
                        <span class="material-symbols-outlined text-xl">dashboard</span>
                        <span class="font-medium text-sm">Dashboard</span>
                    </a>

                    <a href="{{ route('admin.products.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.products.*') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-slate-700/50 hover:text-white' }}">
                        <span class="material-symbols-outlined text-xl">inventory_2</span>
                        <span class="font-medium text-sm">Produtos</span>
                    </a>

                    <a href="{{ route('admin.categories.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.categories.*') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-slate-700/50 hover:text-white' }}">
                        <span class="material-symbols-outlined text-xl">category</span>
                        <span class="font-medium text-sm">Categorias</span>
                    </a>

                    <a href="{{ route('admin.orders.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.orders.*') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-slate-700/50 hover:text-white' }}">
                        <span class="material-symbols-outlined text-xl">shopping_cart</span>
                        <span class="font-medium text-sm">Pedidos</span>
                    </a>

                    <div class="pt-6">
                        <p class="px-2 text-xs font-bold text-slate-500 uppercase tracking-widest mb-4">Configurações
                        </p>
                        <a href="#"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all hover:bg-slate-700/50 hover:text-white">
                            <span class="material-symbols-outlined text-xl">group</span>
                            <span class="font-medium text-sm">Usuários</span>
                        </a>
                        <a href="{{ route('admin.newsletters.index') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.newsletters.*') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-slate-700/50 hover:text-white' }}">
                            <span class="material-symbols-outlined text-xl">mail</span>
                            <span class="font-medium text-sm">Newsletter</span>
                        </a>
                        <a href="{{ route('admin.appearance.index') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.appearance.*') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-slate-700/50 hover:text-white' }}">
                            <span class="material-symbols-outlined text-xl">web</span>
                            <span class="font-medium text-sm">Aparência</span>
                        </a>
                        <a href="{{ route('admin.settings.index') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.settings.*') ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'hover:bg-slate-700/50 hover:text-white' }}">
                            <span class="material-symbols-outlined text-xl">settings</span>
                            <span class="font-medium text-sm">Configurações Gerais</span>
                        </a>
                    </div>
                </nav>

                <!-- Sidebar Footer -->
                <div class="p-4 bg-slate-800/50 border-t border-slate-700/50">
                    <div class="flex items-center gap-3 px-2">
                        <div
                            class="w-10 h-10 rounded-full bg-slate-600 flex items-center justify-center text-white font-bold">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="flex-grow min-w-0">
                            <p class="text-sm font-bold text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-slate-400 truncate">
                                {{ Auth::user()->roles->first()->name ?? 'Admin' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-grow relative overflow-y-auto focus:outline-none custom-scrollbar">
            <!-- Top Header -->
            <header
                class="h-20 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 sticky top-0 z-30 px-6 sm:px-8">
                <div class="flex items-center justify-between h-full">
                    <div class="flex items-center gap-4">
                        <button @click="sidebarOpen = !sidebarOpen"
                            class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                            <span class="material-symbols-outlined">menu</span>
                        </button>
                        <h1 class="text-lg font-bold text-slate-800 dark:text-white">
                            @yield('page_title', 'Administração')</h1>
                    </div>

                    <div class="flex items-center gap-4">
                        <!-- Search -->
                        <div
                            class="hidden sm:flex items-center bg-slate-100 dark:bg-slate-800 px-3 py-2 rounded-xl border border-transparent focus-within:border-primary/50 transition-all">
                            <span class="material-symbols-outlined text-slate-400 text-xl">search</span>
                            <input type="text" placeholder="Pesquisar..."
                                class="bg-transparent border-none focus:ring-0 text-sm w-48 text-slate-600 dark:text-slate-300">
                        </div>

                        <!-- Notifications -->
                        <button
                            class="relative p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                            <span
                                class="material-symbols-outlined text-slate-600 dark:text-slate-400">notifications</span>
                            <span
                                class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white dark:ring-slate-900"></span>
                        </button>

                        <div class="w-px h-6 bg-slate-200 dark:border-slate-800 mx-2"></div>

                        <!-- Profile -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-2 px-3 py-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600 transition-all group">
                                <span
                                    class="material-symbols-outlined text-xl group-hover:rotate-12 transition-transform">logout</span>
                                <span class="text-sm font-bold uppercase tracking-wider hidden sm:inline">Sair</span>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <div class="p-6 sm:p-8">
                {{ $slot }}
            </div>
        </main>
    </div>
    @stack('scripts')
</body>

</html>