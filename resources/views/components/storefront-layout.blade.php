@props([
    'seo' => [],
    'title' => null,
])

@php
    if ($title && empty($seo['title'])) {
        $seo['title'] = $title;
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO & Open Graph Meta Tags --}}
    <x-seo-meta :seo="$seo" />

    <!-- Fonts & Resource Hints -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.tailwindcss.com">
    <link rel="dns-prefetch" href="https://unpkg.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />

    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script defer src="https://unpkg.com/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "{{ $appSettings['primary_color'] ?? '#10b981' }}",
                        "background-light": "#fcfcfc",
                        "background-dark": "{{ $appSettings['secondary_color'] ?? '#101622' }}",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "2xl": "1rem",
                        "3xl": "1.5rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <style type="text/tailwindcss">
        [x-cloak] {
            display: none !important;
        }
        @layer base {
            body { font-family: 'Inter', sans-serif; }
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
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

<body x-data="{ mobileMenuOpen: false }" :class="{ 'overflow-hidden': mobileMenuOpen }"
    class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display antialiased">
    <header
        class="sticky top-0 z-50 w-full bg-white/90 dark:bg-background-dark/90 backdrop-blur-md border-b border-slate-100 dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20 gap-8">
                <div class="flex items-center gap-1 md:gap-2">
                    <button @click="mobileMenuOpen = true"
                        class="md:hidden p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors"
                        aria-label="Abrir menu">
                        <span class="material-symbols-outlined text-2xl">menu</span>
                    </button>
                    <!-- Logo -->
                    <a href="/" class="flex items-center gap-2 shrink-0 group">
                        @if($appSettings['store_logo'])
                            <img src="{{ asset('storage/' . $appSettings['store_logo']) }}" class="w-auto object-contain"
                                style="height: {{ ($appSettings['store_logo_size'] ?? 100) * 0.4 }}px; max-height: 128px;"
                                alt="{{ $appSettings['store_name'] }}">
                        @else
                            <div
                                class="w-8 h-8 md:w-10 md:h-10 bg-primary rounded-xl flex items-center justify-center text-white group-hover:rotate-6 transition-transform">
                                <span
                                    class="material-symbols-outlined text-xl md:text-2xl">{{ $appSettings['store_icon'] ?? 'chair' }}</span>
                            </div>
                            <span
                                class="text-xl md:text-2xl font-bold tracking-tight text-slate-900 dark:text-white uppercase">
                                {{ $appSettings['store_name'] ?? config('app.name') }}
                            </span>
                        @endif
                    </a>
                </div>

                <!-- Navigation -->
                <nav class="hidden md:flex items-center gap-6 lg:gap-8">
                    @foreach(collect($navCategories ?? [])->take(3) as $navCat)
                        <a class="text-sm font-semibold hover:text-primary transition-colors"
                            href="{{ route('search', ['categories' => [$navCat->id]]) }}">{{ $navCat->name }}</a>
                    @endforeach
                    <a class="text-sm font-semibold hover:text-primary transition-colors"
                        href="{{ route('categories.index') }}">Categorias</a>
                    <a class="text-sm font-semibold hover:text-primary transition-colors"
                        href="{{ route('customer.orders') }}">Meus Pedidos</a>
                </nav>

                <!-- Search (oculto na Home para evitar busca duplicada com a barra do banner) -->
                <div class="flex-1 max-w-sm {{ request()->is('/') ? 'hidden' : 'hidden lg:block' }}">
                    <form action="{{ route('search') }}" method="GET" class="relative">
                        <span
                            class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xl">search</span>
                        <input name="q" value="{{ request('q') }}"
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full py-2 pl-10 pr-4 text-sm focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all"
                            placeholder="Buscar coleções exclusivas..." type="text" />
                    </form>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-1 md:gap-3">
                    @auth
                        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Manager'))
                            <a href="{{ route('admin.dashboard') }}"
                                class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors"
                                title="Painel Admin">
                                <span class="material-symbols-outlined text-2xl">admin_panel_settings</span>
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}"
                            class="text-sm font-bold hover:text-primary transition-colors px-2">Entrar</a>
                    @endauth

                    <a href="{{ route('cart.index') }}"
                        class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors relative">
                        <span class="material-symbols-outlined text-2xl">shopping_cart</span>
                        @if(count(session()->get('cart', [])) > 0)
                            <span id="cart-count-badge"
                                class="absolute top-1 right-1 w-4 h-4 bg-primary text-[10px] font-bold text-white rounded-full flex items-center justify-center">
                                {{ count(session()->get('cart', [])) }}
                            </span>
                        @endif
                    </a>

                    @auth
                        <a href="{{ route('customer.dashboard') }}"
                            class="h-9 w-9 md:h-10 md:w-10 rounded-full overflow-hidden border-2 border-slate-100 dark:border-slate-700 ml-1 shadow-sm group">
                            <div
                                class="w-full h-full bg-primary/10 flex items-center justify-center text-primary font-bold group-hover:bg-primary group-hover:text-white transition-colors">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Menu Overlay -->
    <template x-teleport="body">
        <div x-show="mobileMenuOpen" x-cloak class="fixed inset-0 z-[100] md:hidden" role="dialog" aria-modal="true">

            <!-- Backdrop -->
            <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
                @click="mobileMenuOpen = false"></div>

            <!-- Menu Content -->
            <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
                class="relative w-4/5 max-w-sm h-full bg-white dark:bg-slate-900 shadow-2xl flex flex-col overflow-hidden">

                <div class="p-6 flex items-center justify-between border-b border-slate-100 dark:border-slate-800">
                    <a href="/" class="flex items-center gap-2" @click="mobileMenuOpen = false">
                        @if($appSettings['store_logo'])
                            <img src="{{ asset('storage/' . $appSettings['store_logo']) }}"
                                class="h-8 w-auto object-contain" alt="{{ $appSettings['store_name'] }}">
                        @else
                            <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center text-white">
                                <span
                                    class="material-symbols-outlined text-xl">{{ $appSettings['store_icon'] ?? 'chair' }}</span>
                            </div>
                            <span class="text-lg font-bold tracking-tight text-slate-900 dark:text-white uppercase">
                                {{ $appSettings['store_name'] ?? config('app.name') }}
                            </span>
                        @endif
                    </a>
                    <button @click="mobileMenuOpen = false"
                        class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors text-slate-500">
                        <span class="material-symbols-outlined text-2xl">close</span>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto py-8 px-6">
                    <!-- Search inside mobile menu -->
                    <div class="mb-10">
                        <form action="{{ route('search') }}" method="GET" class="relative">
                            <span
                                class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xl">search</span>
                            <input name="q" value="{{ request('q') }}"
                                class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-800 rounded-xl py-3 pl-10 pr-4 text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all"
                                placeholder="Buscar produtos..." type="text" />
                        </form>
                    </div>

                    <nav class="space-y-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-4">Navegação</p>
                        <a href="/"
                            class="flex items-center gap-3 px-4 py-3 text-slate-700 dark:text-slate-200 font-semibold rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-primary transition-all group"
                            @click="mobileMenuOpen = false">
                            <span
                                class="material-symbols-outlined text-xl text-slate-400 group-hover:text-primary">home</span>
                            Início
                        </a>
                        <a href="{{ route('categories.index') }}"
                            class="flex items-center gap-3 px-4 py-3 text-slate-700 dark:text-slate-200 font-semibold rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-primary transition-all group"
                            @click="mobileMenuOpen = false">
                            <span
                                class="material-symbols-outlined text-xl text-slate-400 group-hover:text-primary">category</span>
                            Categorias
                        </a>
                        <a href="{{ route('home.family') }}"
                            class="flex items-center gap-3 px-4 py-3 text-slate-700 dark:text-slate-200 font-semibold rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-primary transition-all group"
                            @click="mobileMenuOpen = false">
                            <span
                                class="material-symbols-outlined text-xl text-slate-400 group-hover:text-primary">history</span>
                            Nossa História
                        </a>
                        <a href="{{ route('customer.orders') }}"
                            class="flex items-center gap-3 px-4 py-3 text-slate-700 dark:text-slate-200 font-semibold rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-primary transition-all group"
                            @click="mobileMenuOpen = false">
                            <span
                                class="material-symbols-outlined text-xl text-slate-400 group-hover:text-primary">package_2</span>
                            Meus Pedidos
                        </a>

                        <div class="h-px bg-slate-100 dark:bg-slate-800 my-6"></div>

                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-4">Departamentos
                        </p>
                        @foreach($navCategories ?? [] as $navCat)
                            <a href="{{ route('search', ['categories' => [$navCat->id]]) }}"
                                class="flex items-center justify-between px-4 py-3 text-slate-600 dark:text-slate-300 font-medium rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-primary transition-all"
                                @click="mobileMenuOpen = false">
                                {{ $navCat->name }}
                                <span
                                    class="material-symbols-outlined text-sm opacity-0 group-hover:opacity-100 transition-opacity">chevron_right</span>
                            </a>
                        @endforeach
                    </nav>
                </div>

                <!-- Footer elements inside menu -->
                <div class="p-6 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50">
                    @auth
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white font-bold">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900 dark:text-white">{{ Auth::user()->name }}</p>
                                <a href="{{ route('customer.dashboard') }}"
                                    class="text-xs text-primary font-semibold hover:underline"
                                    @click="mobileMenuOpen = false">Ver perfil</a>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}"
                            class="flex items-center justify-center gap-2 w-full bg-primary text-white font-bold py-3 rounded-xl shadow-lg shadow-primary/20 mb-4"
                            @click="mobileMenuOpen = false">
                            <span class="material-symbols-outlined text-xl">login</span>
                            Entrar na Conta
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </template>

    <main>
        {{ $slot }}
    </main>

    <footer class="bg-white dark:bg-slate-950 border-t border-slate-100 dark:border-slate-900 pt-10 sm:pt-16 pb-8 sm:pb-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-8 lg:gap-12 mb-8 sm:mb-12">
                {{-- Coluna 1: Marca & Dados da Loja --}}
                <div class="lg:col-span-4 space-y-4">
                    <div class="flex items-center gap-2">
                        @if($appSettings['store_logo'])
                            <img src="{{ str_starts_with($appSettings['store_logo'], 'http') ? $appSettings['store_logo'] : asset('storage/' . $appSettings['store_logo']) }}"
                                class="w-auto object-contain"
                                style="height: {{ min(48, ($appSettings['store_logo_size'] ?? 100) * 0.35) }}px; max-height: 52px;"
                                alt="{{ $appSettings['store_name'] }}">
                        @else
                            <div class="size-9 bg-primary rounded-xl flex items-center justify-center text-white">
                                <span class="material-symbols-outlined text-lg">{{ $appSettings['store_icon'] ?? 'steps' }}</span>
                            </div>
                            <span class="text-xl font-black tracking-tight text-slate-900 dark:text-white uppercase">
                                {{ $appSettings['store_name'] ?? config('app.name') }}
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed max-w-sm">
                        {{ $appSettings['store_footer_description'] ?? 'Sua loja especializada em tênis de corrida, treino e performance das melhores marcas mundiais, com envio rápido e garantia de originalidade.' }}
                    </p>
                    <div class="text-[11px] text-slate-400 space-y-0.5">
                        @if(!empty($appSettings['store_company_name']))
                            <p class="font-bold text-slate-300">{{ $appSettings['store_company_name'] }}</p>
                        @endif
                        @if(!empty($appSettings['store_document']))
                            <p>CNPJ/CPF: {{ $appSettings['store_document'] }}</p>
                        @endif
                        @if(!empty($appSettings['store_city']))
                            <p>{{ !empty($appSettings['store_street']) ? $appSettings['store_street'] . ', ' . ($appSettings['store_number'] ?? 'S/N') . ' • ' : '' }}{{ !empty($appSettings['store_neighborhood']) ? $appSettings['store_neighborhood'] . ' • ' : '' }}{{ $appSettings['store_city'] }}/{{ $appSettings['store_state'] }}{{ !empty($appSettings['store_zip']) ? ' • CEP: ' . $appSettings['store_zip'] : '' }}</p>
                        @endif
                        @if(!empty($appSettings['store_phone']))
                            <p>Telefone: {{ $appSettings['store_phone'] }}</p>
                        @endif
                        @if(!empty($appSettings['store_email']))
                            <p>E-mail: {{ $appSettings['store_email'] }}</p>
                        @endif
                        @if(!empty($appSettings['store_opening_hours']))
                            <p class="text-[10px] text-slate-500">Horário: {{ $appSettings['store_opening_hours'] }}</p>
                        @endif
                    </div>
                    {{-- Redes Sociais --}}
                    @php
                        $whatsPhone = $appSettings['store_whatsapp'] ?? $appSettings['store_phone'] ?? '5561985220100';
                        $cleanWhats = preg_replace('/\D/', '', $whatsPhone);
                        if ($cleanWhats && !str_starts_with($cleanWhats, '55')) {
                            $cleanWhats = '55' . $cleanWhats;
                        }
                        $whatsMsg = $appSettings['store_whatsapp_message'] ?? 'Olá! Gostaria de mais informações.';
                        $whatsFooter = 'https://wa.me/' . ($cleanWhats ?: '5561985220100') . '?text=' . urlencode($whatsMsg);

                        $instagram = $appSettings['store_instagram'] ?? '';
                        $instagramUrl = '';
                        if ($instagram) {
                            if (str_starts_with($instagram, 'http://') || str_starts_with($instagram, 'https://')) {
                                $instagramUrl = $instagram;
                            } else {
                                $cleanInsta = ltrim(trim($instagram), '@');
                                $instagramUrl = 'https://instagram.com/' . $cleanInsta;
                            }
                        }

                        $facebookUrl = $appSettings['store_facebook'] ?? '';
                        $tiktokUrl = $appSettings['store_tiktok'] ?? '';
                        $youtubeUrl = $appSettings['store_youtube'] ?? '';
                    @endphp
                    <div class="flex items-center gap-2.5 pt-1 flex-wrap">
                        @if($cleanWhats)
                            <a class="size-8 rounded-xl bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-[#25D366] hover:text-white transition-all shadow-sm"
                                href="{{ $whatsFooter }}" target="_blank" title="WhatsApp">
                                <svg class="size-4 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.72 1.144 3.763 1.748 5.8 1.748h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
                                </svg>
                            </a>
                        @endif
                        @if($instagramUrl)
                            <a class="size-8 rounded-xl bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-[#E4405F] hover:text-white transition-all shadow-sm"
                                href="{{ $instagramUrl }}" target="_blank" title="Instagram">
                                <svg class="size-4 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                            </a>
                        @endif
                        @if($facebookUrl)
                            <a class="size-8 rounded-xl bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-[#1877F2] hover:text-white transition-all shadow-sm"
                                href="{{ $facebookUrl }}" target="_blank" title="Facebook">
                                <svg class="size-4 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </a>
                        @endif
                        @if($tiktokUrl)
                            <a class="size-8 rounded-xl bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-black hover:text-white transition-all shadow-sm"
                                href="{{ $tiktokUrl }}" target="_blank" title="TikTok">
                                <svg class="size-4 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.24 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                                </svg>
                            </a>
                        @endif
                        @if($youtubeUrl)
                            <a class="size-8 rounded-xl bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-[#FF0000] hover:text-white transition-all shadow-sm"
                                href="{{ $youtubeUrl }}" target="_blank" title="YouTube">
                                <svg class="size-4 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Coluna 2 e 3: Links Institucionais e Atendimento (No mobile: grid lado a lado!) --}}
                <div class="lg:col-span-4 grid grid-cols-2 gap-6">
                    <div>
                        <h5 class="font-bold text-slate-900 dark:text-white mb-3 text-xs uppercase tracking-wider">
                            Institucional
                        </h5>
                        <ul class="space-y-2 text-xs text-slate-500 dark:text-slate-400 font-medium">
                            <li><a class="hover:text-primary transition-colors" href="/">Página Inicial</a></li>
                            <li><a class="hover:text-primary transition-colors" href="{{ route('categories.index') }}">Categorias</a></li>
                            <li><a class="hover:text-primary transition-colors" href="{{ route('search') }}">Todos os Tênis</a></li>
                            <li><a class="hover:text-primary transition-colors" href="{{ route('customer.orders') }}">Meus Pedidos</a></li>
                        </ul>
                    </div>

                    <div>
                        <h5 class="font-bold text-slate-900 dark:text-white mb-3 text-xs uppercase tracking-wider">
                            Atendimento
                        </h5>
                        <ul class="space-y-2 text-xs text-slate-500 dark:text-slate-400 font-medium">
                            <li><a class="hover:text-primary transition-colors" href="{{ $whatsFooter }}" target="_blank">Fale no WhatsApp</a></li>
                            <li><a class="hover:text-primary transition-colors" href="#">Prazos & Frete</a></li>
                            <li><a class="hover:text-primary transition-colors" href="#">Trocas e Devoluções</a></li>
                            <li><a class="hover:text-primary transition-colors" href="#">Retirada na Loja</a></li>
                        </ul>
                    </div>
                </div>

                {{-- Coluna 4: Newsletter Compacta --}}
                <div class="lg:col-span-4 space-y-3">
                    <h5 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider">
                        Novidades & Ofertas
                    </h5>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Receba cupons exclusivos e lançamentos de tênis direto no seu e-mail.
                    </p>
                    <form class="space-y-2" x-data="{ 
                        email: '', 
                        loading: false, 
                        message: '', 
                        success: false,
                        subscribe() {
                            if (!this.email) return;
                            this.loading = true;
                            this.message = '';
                            
                            fetch('{{ route('newsletter.subscribe') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ email: this.email })
                            })
                            .then(response => response.json())
                            .then(data => {
                                this.loading = false;
                                this.message = data.message;
                                this.success = data.success;
                                if (data.success) this.email = '';
                            })
                            .catch(() => {
                                this.loading = false;
                                this.message = 'Erro ao processar inscrição.';
                                this.success = false;
                            });
                        }
                    }" @submit.prevent="subscribe()">
                        <div class="flex items-center gap-1.5">
                            <input
                                class="flex-1 min-w-0 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-3.5 py-2.5 text-xs focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all font-medium"
                                placeholder="Seu melhor e-mail..." type="email" x-model="email" required />
                            <button type="submit" :disabled="loading"
                                class="bg-primary text-white font-bold py-2.5 px-4 rounded-xl text-xs transition-all shadow-md shadow-primary/20 disabled:opacity-50 shrink-0 flex items-center justify-center">
                                <template x-if="!loading"><span>Cadastrar</span></template>
                                <template x-if="loading"><span class="material-symbols-outlined animate-spin text-sm">sync</span></template>
                            </button>
                        </div>
                        <p x-show="message" x-cloak :class="success ? 'text-emerald-500' : 'text-red-500'"
                            class="text-[11px] font-bold" x-text="message"></p>
                    </form>
                </div>
            </div>

            {{-- Barra Inferior: Pagamentos & Copyright --}}
            <div class="pt-6 border-t border-slate-100 dark:border-slate-900 flex flex-col sm:flex-row justify-between items-center gap-4 text-center sm:text-left">
                <div class="flex flex-col sm:flex-row items-center gap-2 sm:gap-4 text-[11px] text-slate-400 font-medium">
                    <p>© {{ date('Y') }} {{ $appSettings['store_name'] ?? 'Denner Tênis' }}. Todos os direitos reservados.</p>
                </div>

                {{-- Formas de Pagamento --}}
                <div class="flex items-center gap-2 flex-wrap justify-center text-[10px] font-black text-slate-500">
                    <span class="px-2 py-1 rounded bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-emerald-600 flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">qr_code_2</span> PIX
                    </span>
                    <span class="px-2 py-1 rounded bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">credit_card</span> Cartão até 10x
                    </span>
                    <span class="px-2 py-1 rounded bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs">lock</span> 100% Seguro
                    </span>
                </div>
            </div>
        </div>
    </footer>

    {{-- Botão Flutuante do WhatsApp --}}
    @php
        $rawFloatingPhone = $appSettings['store_whatsapp'] ?? $appSettings['store_phone'] ?? '5561985220100';
        $cleanFloatingPhone = preg_replace('/\D/', '', $rawFloatingPhone);
        if ($cleanFloatingPhone && !str_starts_with($cleanFloatingPhone, '55')) {
            $cleanFloatingPhone = '55' . $cleanFloatingPhone;
        }
        $floatingMsg = $appSettings['store_whatsapp_message'] ?? 'Olá! Gostaria de mais informações.';
        $whatsUrl = 'https://wa.me/' . ($cleanFloatingPhone ?: '5561985220100') . '?text=' . urlencode($floatingMsg);
    @endphp
    <a href="{{ $whatsUrl }}" target="_blank" rel="noopener noreferrer"
        aria-label="Fale conosco no WhatsApp"
        class="fixed bottom-24 right-4 sm:bottom-6 sm:right-6 z-50 flex items-center justify-center w-14 h-14 sm:w-16 sm:h-16 bg-[#25D366] hover:bg-[#20bd5a] text-white rounded-full shadow-2xl shadow-emerald-600/40 hover:scale-110 active:scale-95 transition-all duration-300 group">
        <span class="absolute -top-1 -right-1 flex size-3.5">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-300 opacity-75"></span>
            <span class="relative inline-flex rounded-full size-3.5 bg-emerald-400"></span>
        </span>
        <svg class="w-8 h-8 fill-current transition-transform group-hover:rotate-6" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.72 1.144 3.763 1.748 5.8 1.748h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
        </svg>
    </a>

    @stack('scripts')
</body>

</html>