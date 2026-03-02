<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Minha Conta') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm rounded-2xl">
                <div class="p-8 text-slate-900 dark:text-slate-100">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-3xl">account_circle</span>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold">Olá, {{ Auth::user()->name }}!</h1>
                            <p class="text-slate-500 text-sm">Bem-vindo à sua área exclusiva.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div
                            class="p-6 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800 hover:border-primary/30 transition-colors group">
                            <div class="flex items-center gap-3 mb-3">
                                <span
                                    class="material-symbols-outlined text-primary group-hover:scale-110 transition-transform">shopping_bag</span>
                                <h3 class="font-bold text-lg">Pedidos recentes</h3>
                            </div>
                            <p class="text-sm text-slate-500">Você ainda não tem pedidos realizados. Comece a comprar
                                agora!</p>
                            <a href="/" class="inline-block mt-4 text-sm font-bold text-primary hover:underline">Ir para
                                a loja &rarr;</a>
                        </div>

                        <div
                            class="p-6 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800 hover:border-primary/30 transition-colors group">
                            <div class="flex items-center gap-3 mb-3">
                                <span
                                    class="material-symbols-outlined text-primary group-hover:scale-110 transition-transform">favorite</span>
                                <h3 class="font-bold text-lg">Favoritos</h3>
                            </div>
                            <p class="text-sm text-slate-500">Sua lista de desejos está vazia. Salve seus itens
                                preferidos!</p>
                            <a href="/"
                                class="inline-block mt-4 text-sm font-bold text-primary hover:underline">Explorar
                                catálogo &rarr;</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>