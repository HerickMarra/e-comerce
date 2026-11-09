<x-storefront-layout :seo="$seo">
    <div class="bg-slate-50/50 dark:bg-slate-950 min-h-screen py-6 sm:py-10">
        <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">

            {{-- Header da Página --}}
            <header class="mb-6 sm:mb-10 text-center max-w-2xl mx-auto">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-wider mb-3">
                    <span class="material-symbols-outlined text-sm">explore</span>
                    Feed Infinito de Descoberta
                </div>
                <h1 class="text-2xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">
                    Explorar
                </h1>
                <p class="text-xs sm:text-base text-slate-500 dark:text-slate-400 mt-1 sm:mt-2">
                    Descubra seu próximo tênis favorito
                </p>

                {{-- Barra de Ações Rápidas --}}
                <div class="mt-4 flex items-center justify-center gap-2 flex-wrap text-xs">
                    <span class="px-3 py-1.5 rounded-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 font-semibold shadow-xs flex items-center gap-1.5">
                        <span class="size-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span id="explore-counter-text">Carregando catálogo...</span>
                    </span>

                    <a href="{{ route('explore') }}"
                        class="px-3.5 py-1.5 rounded-full bg-slate-900 hover:bg-primary text-white dark:bg-slate-800 dark:hover:bg-primary font-bold shadow-xs transition-all flex items-center gap-1.5"
                        title="Gerar uma nova ordem aleatória de produtos">
                        <span class="material-symbols-outlined text-sm">shuffle</span>
                        Nova Ordem
                    </a>
                </div>
            </header>

            {{-- Estado Vazio (sem produtos) --}}
            @if($products->isEmpty())
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-10 sm:p-16 text-center border border-slate-100 dark:border-slate-800 max-w-md mx-auto shadow-sm">
                    <div class="w-16 h-16 bg-primary/10 text-primary rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-3xl">steps</span>
                    </div>
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Nenhum tênis disponível no momento</h2>
                    <p class="text-xs sm:text-sm text-slate-500 mb-6">Estamos abastecendo o catálogo com novos lançamentos. Volte em breve!</p>
                    <a href="/" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-white text-xs font-bold shadow-md hover:opacity-90 transition-all">
                        <span class="material-symbols-outlined text-sm">home</span>
                        Ir para a Página Inicial
                    </a>
                </div>
            @else
                {{-- Feed de Produtos --}}
                {{-- Grid Responsivo: 2 colunas mobile (até 320px), 3 tablet, 4 desktop, 5 telas grandes --}}
                <div id="explore-grid"
                     class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-5 gap-3 sm:gap-4 md:gap-6">
                    @include('explore._cards_list', ['products' => $products])
                </div>

                {{-- Skeletons de Carregamento (Ocultos por padrão) --}}
                <div id="explore-skeletons"
                     class="hidden grid-cols-2 md:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-5 gap-3 sm:gap-4 md:gap-6 mt-3 sm:mt-4 md:mt-6"
                     aria-hidden="true">
                    @for($i = 0; $i < 4; $i++)
                        <div class="bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-100 dark:border-slate-800 p-2.5 sm:p-4 space-y-3 animate-pulse">
                            <div class="aspect-[3/4] bg-slate-200 dark:bg-slate-800 rounded-xl sm:rounded-2xl"></div>
                            <div class="space-y-1.5">
                                <div class="h-3 bg-slate-200 dark:bg-slate-800 rounded w-1/3"></div>
                                <div class="h-4 bg-slate-200 dark:bg-slate-800 rounded w-4/5"></div>
                            </div>
                            <div class="pt-2 flex justify-between items-center border-t border-slate-100 dark:border-slate-800">
                                <div class="h-5 bg-slate-200 dark:bg-slate-800 rounded w-1/2"></div>
                                <div class="w-8 h-8 bg-slate-200 dark:bg-slate-800 rounded-lg"></div>
                            </div>
                        </div>
                    @endfor
                </div>

                {{-- Sentinel para IntersectionObserver --}}
                <div id="explore-sentinel" class="py-8 flex flex-col items-center justify-center">
                    <div id="explore-spinner" class="hidden items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
                        <svg class="animate-spin h-5 w-5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Carregando mais tênis...</span>
                    </div>

                    <div id="explore-error" class="hidden text-center py-4">
                        <p class="text-xs text-red-500 font-semibold mb-2">Não foi possível carregar mais produtos no momento.</p>
                        <button type="button" id="explore-retry-btn" class="px-4 py-2 bg-primary text-white text-xs font-bold rounded-xl shadow-xs hover:opacity-90 transition-all">
                            Tentar Novamente
                        </button>
                    </div>
                </div>

                {{-- Fallback para SEO e Rastreadores sem JavaScript --}}
                <noscript>
                    <nav class="mt-12 text-center py-6 border-t border-slate-200 dark:border-slate-800" aria-label="Paginação para rastreadores">
                        <p class="text-xs text-slate-500 mb-3">Navegue pelas páginas do catálogo:</p>
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('explore', ['page' => 1, 'seed' => $seed]) }}" class="px-3 py-1 bg-white border border-slate-200 rounded text-xs font-bold">Página 1</a>
                            <a href="{{ route('explore', ['page' => 2, 'seed' => $seed]) }}" class="px-3 py-1 bg-white border border-slate-200 rounded text-xs font-bold">Página 2</a>
                            <a href="{{ route('explore', ['page' => 3, 'seed' => $seed]) }}" class="px-3 py-1 bg-white border border-slate-200 rounded text-xs font-bold">Página 3</a>
                        </div>
                    </nav>
                </noscript>
            @endif

        </div>
    </div>

    {{-- Script de Rolagem Infinita e Restauração de Posição --}}
    @push('scripts')
    <script>
        (function() {
            const config = {
                seed: {{ (int) $seed }},
                initialPage: {{ (int) $page }},
                perPage: {{ (int) $perPage }},
                totalProducts: {{ (int) $total }},
                apiUrl: "{{ route('explore') }}"
            };

            const grid = document.getElementById('explore-grid');
            const sentinel = document.getElementById('explore-sentinel');
            const skeletons = document.getElementById('explore-skeletons');
            const spinner = document.getElementById('explore-spinner');
            const errorContainer = document.getElementById('explore-error');
            const retryBtn = document.getElementById('explore-retry-btn');
            const counterText = document.getElementById('explore-counter-text');

            if (!grid || !sentinel) return;

            let currentPage = config.initialPage;
            let currentCycle = {{ (int) $cycle }};
            let loadedCount = grid.querySelectorAll('article').length;
            let isLoading = false;
            let abortController = null;
            let currentSeed = config.seed;

            function updateCounter() {
                if (!counterText) return;
                if (config.totalProducts <= 0) {
                    counterText.textContent = 'Nenhum produto';
                    return;
                }
                if (currentCycle > 1) {
                    counterText.textContent = `${loadedCount} tênis explorados • Ciclo ${currentCycle}`;
                } else {
                    counterText.textContent = `${loadedCount} de ${config.totalProducts} tênis explorados`;
                }
            }

            updateCounter();

            // Sincroniza a URL com a seed atual sem recarregar
            if (window.history && window.history.replaceState) {
                const url = new URL(window.location.href);
                if (url.searchParams.get('seed') !== String(currentSeed)) {
                    url.searchParams.set('seed', currentSeed);
                    window.history.replaceState({ seed: currentSeed }, '', url.toString());
                }
            }

            // Preservar estado na navegação para voltar exatamente onde parou
            function saveState() {
                try {
                    sessionStorage.setItem('explore_state_' + currentSeed, JSON.stringify({
                        page: currentPage,
                        cycle: currentCycle,
                        scrollY: window.scrollY || window.pageYOffset,
                        loadedCount: loadedCount
                    }));
                } catch (e) {}
            }

            window.addEventListener('beforeunload', saveState);

            // Carrega próximo lote
            async function loadNextPage() {
                if (isLoading) return;
                isLoading = true;

                if (skeletons) {
                    skeletons.classList.remove('hidden');
                    skeletons.classList.add('grid');
                }
                if (spinner) spinner.classList.remove('hidden');
                if (errorContainer) errorContainer.classList.add('hidden');

                const nextPage = currentPage + 1;
                const fetchUrl = `${config.apiUrl}?ajax=1&page=${nextPage}&seed=${currentSeed}&per_page=${config.perPage}`;

                if (abortController) {
                    abortController.abort();
                }
                abortController = new AbortController();

                try {
                    const response = await fetch(fetchUrl, {
                        signal: abortController.signal,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) throw new Error('Falha na resposta do servidor');

                    const data = await response.json();

                    if (data && data.html && data.html.trim() !== '') {
                        // Renderiza os novos cards com animação suave
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = data.html;

                        const newCards = Array.from(tempDiv.children);
                        newCards.forEach(card => {
                            card.classList.add('opacity-0', 'translate-y-4', 'transition-all', 'duration-500');
                            grid.appendChild(card);
                            // Trigger reflow para iniciar a transição
                            requestAnimationFrame(() => {
                                card.classList.remove('opacity-0', 'translate-y-4');
                            });
                        });

                        currentPage = data.page;
                        currentCycle = data.cycle || currentCycle;
                        loadedCount += newCards.length;
                        updateCounter();
                        saveState();
                    }
                } catch (err) {
                    if (err.name !== 'AbortError') {
                        console.error('Erro ao carregar mais produtos:', err);
                        if (errorContainer) errorContainer.classList.remove('hidden');
                    }
                } finally {
                    isLoading = false;
                    if (skeletons) {
                        skeletons.classList.add('hidden');
                        skeletons.classList.remove('grid');
                    }
                    if (spinner) spinner.classList.add('hidden');
                }
            }

            // Configuração do IntersectionObserver com margem antecipada para rolagem contínua
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !isLoading) {
                        loadNextPage();
                    }
                });
            }, {
                root: null,
                rootMargin: '300px', // Inicia o download 300px antes do final
                threshold: 0.05
            });

            observer.observe(sentinel);

            if (retryBtn) {
                retryBtn.addEventListener('click', () => {
                    loadNextPage();
                });
            }

            // Restauração de scroll e posição ao voltar no histórico
            try {
                const saved = sessionStorage.getItem('explore_state_' + currentSeed);
                if (saved) {
                    const state = JSON.parse(saved);
                    if (state && state.scrollY) {
                        setTimeout(() => {
                            window.scrollTo({
                                top: state.scrollY,
                                behavior: 'smooth'
                            });
                        }, 150);
                    }
                }
            } catch (e) {}
        })();
    </script>
    @endpush
</x-storefront-layout>
