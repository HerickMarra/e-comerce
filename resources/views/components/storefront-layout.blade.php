<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $appSettings['store_name'] ?? config('app.name', 'VerveHome') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
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
    <style type="text/tailwindcss">
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

<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display antialiased">
    <header
        class="sticky top-0 z-50 w-full bg-white/90 dark:bg-background-dark/90 backdrop-blur-md border-b border-slate-100 dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20 gap-8">
                <!-- Logo -->
                <a href="/" class="flex items-center gap-2 shrink-0 group">
                    @if($appSettings['store_logo'])
                        <img src="{{ asset('storage/' . $appSettings['store_logo']) }}" class="w-auto object-contain"
                            style="height: {{ ($appSettings['store_logo_size'] ?? 100) * 0.4 }}px; max-height: 64px;"
                            alt="{{ $appSettings['store_name'] }}">
                    @else
                        <div
                            class="w-8 h-8 md:w-10 md:h-10 bg-primary rounded-xl flex items-center justify-center text-white group-hover:rotate-6 transition-transform">
                            <span
                                class="material-symbols-outlined text-xl md:text-2xl">{{ $appSettings['store_icon'] ?? 'chair' }}</span>
                        </div>
                        <span class="text-xl md:text-2xl font-bold tracking-tight text-slate-900 dark:text-white uppercase">
                            {{ $appSettings['store_name'] ?? config('app.name') }}
                        </span>
                    @endif
                </a>

                <!-- Navigation -->
                <nav class="hidden md:flex items-center gap-10">
                    @foreach($navCategories ?? [] as $navCat)
                        <a class="text-sm font-semibold hover:text-primary transition-colors"
                            href="{{ route('search', ['categories' => [$navCat->id]]) }}">{{ $navCat->name }}</a>
                    @endforeach
                    <a class="text-sm font-semibold hover:text-primary transition-colors"
                        href="{{ route('categories.index') }}">Categorias</a>
                    <a class="text-sm font-semibold hover:text-primary transition-colors"
                        href="{{ route('customer.orders') }}">Meus Pedidos</a>
                </nav>

                <!-- Search -->
                <div class="flex-1 max-w-sm hidden lg:block">
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

    <main>
        {{ $slot }}
    </main>

    <footer class="bg-white dark:bg-slate-950 border-t border-slate-100 dark:border-slate-900 pt-20 pb-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-12 mb-16">
                <div class="lg:col-span-4">
                    <div class="flex items-center gap-2 mb-8">
                        @if($appSettings['store_logo'])
                            <img src="{{ asset('storage/' . $appSettings['store_logo']) }}" class="w-auto object-contain"
                                style="height: {{ ($appSettings['store_logo_size'] ?? 100) * 0.35 }}px; max-height: 56px;"
                                alt="{{ $appSettings['store_name'] }}">
                        @else
                            <div class="w-9 h-9 bg-primary rounded-lg flex items-center justify-center text-white">
                                <span
                                    class="material-symbols-outlined text-xl">{{ $appSettings['store_icon'] ?? 'chair' }}</span>
                            </div>
                            <span class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white uppercase">
                                {{ $appSettings['store_name'] ?? config('app.name') }}
                            </span>
                        @endif
                    </div>
                    <p class="text-slate-500 dark:text-slate-400 leading-relaxed mb-8 max-w-sm">
                        Conceito e conforto para a alma moderna. Mobiliário de alta qualidade que une estética impecável
                        e funcionalidade para o seu cotidiano.
                    </p>
                    <div class="flex gap-4">
                        <a class="w-10 h-10 rounded-full bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-primary hover:text-white transition-all shadow-sm"
                            href="#">
                            <span class="material-symbols-outlined text-lg">public</span>
                        </a>
                        <a class="w-10 h-10 rounded-full bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-primary hover:text-white transition-all shadow-sm"
                            href="#">
                            <span class="material-symbols-outlined text-lg">camera</span>
                        </a>
                        <a class="w-10 h-10 rounded-full bg-slate-50 dark:bg-slate-900 flex items-center justify-center text-slate-600 dark:text-slate-400 hover:bg-primary hover:text-white transition-all shadow-sm"
                            href="#">
                            <span class="material-symbols-outlined text-lg">movie</span>
                        </a>
                    </div>
                </div>
                <div class="lg:col-span-2">
                    <h5 class="font-bold text-slate-900 dark:text-white mb-8 uppercase text-xs tracking-widest">
                        Institucional
                    </h5>
                    <ul class="space-y-4 text-sm text-slate-500 dark:text-slate-400 font-medium">
                        <li><a class="hover:text-primary transition-colors" href="#">Nossa Marca</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">Sustentabilidade</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">Showrooms</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">Artesanato Curado</a></li>
                    </ul>
                </div>
                <div class="lg:col-span-2">
                    <h5 class="font-bold text-slate-900 dark:text-white mb-8 uppercase text-xs tracking-widest">
                        Atendimento</h5>
                    <ul class="space-y-4 text-sm text-slate-500 dark:text-slate-400 font-medium">
                        <li><a class="hover:text-primary transition-colors" href="#">Envio e Entrega</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">Trocas e Devoluções</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">Garantia de Qualidade</a></li>
                        <li><a class="hover:text-primary transition-colors" href="#">Fale Conosco</a></li>
                    </ul>
                </div>
                <div class="lg:col-span-4">
                    <h5 class="font-bold text-slate-900 dark:text-white mb-8 uppercase text-xs tracking-widest">
                        Inspire-se</h5>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Participe da nossa lista exclusiva para
                        dicas de design e novos lançamentos.</p>
                    <form class="flex flex-col gap-3" x-data="{ 
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
                            .catch(error => {
                                this.loading = false;
                                this.message = 'Ocorreu um erro ao processar sua inscrição. Tente novamente.';
                                this.success = false;
                            });
                        }
                    }" @submit.prevent="subscribe()">
                        <div class="relative">
                            <input
                                class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all"
                                placeholder="seu@email.com" type="email" x-model="email" required />
                        </div>
                        <button type="submit" :disabled="loading"
                            class="bg-primary text-white font-bold py-3 px-6 rounded-xl transition-all shadow-lg shadow-primary/20 disabled:opacity-50 flex items-center justify-center gap-2">
                            <template x-if="!loading"><span>Inscrever-se</span></template>
                            <template x-if="loading"><span
                                    class="material-symbols-outlined animate-spin text-sm">sync</span></template>
                        </button>
                        <p x-show="message" x-cloak :class="success ? 'text-emerald-500' : 'text-red-500'"
                            class="text-xs font-bold mt-2 antialiased" x-text="message"></p>
                    </form>
                </div>
            </div>
            <div
                class="pt-10 border-t border-slate-100 dark:border-slate-900 flex flex-col md:flex-row justify-between items-center gap-8">
                <div class="flex flex-col md:flex-row items-center gap-4 text-xs font-medium text-slate-400">
                    <p>© {{ date('Y') }} {{ $appSettings['store_name'] ?? config('app.name') }}</p>
                    <div class="hidden md:block w-1 h-1 bg-slate-300 rounded-full"></div>
                    <a class="hover:text-primary" href="#">Política de Privacidade</a>
                    <div class="hidden md:block w-1 h-1 bg-slate-300 rounded-full"></div>
                    <a class="hover:text-primary" href="#">Termos de Serviço</a>
                </div>
                <div class="flex items-center gap-5 opacity-60 grayscale hover:grayscale-0 transition-all duration-300">
                    <!-- Placeholder payment icons -->
                    <div class="h-4 w-8 bg-slate-200 rounded"></div>
                    <div class="h-4 w-8 bg-slate-200 rounded"></div>
                    <div class="h-4 w-8 bg-slate-200 rounded"></div>
                </div>
            </div>
        </div>
    </footer>
    @stack('scripts')
</body>

</html>