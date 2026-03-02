@props(['active' => 'dashboard'])

<aside class="w-full lg:w-64 shrink-0">
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-4 shadow-sm">
        <nav class="space-y-1">
            <a href="{{ route('customer.dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm transition-colors rounded-xl {{ $active === 'dashboard' ? 'font-bold text-primary bg-primary/5' : 'font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                <span class="material-symbols-outlined text-xl">dashboard</span>
                Painel
            </a>
            <a href="{{ route('customer.orders') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm transition-colors rounded-xl {{ $active === 'orders' ? 'font-bold text-primary bg-primary/5' : 'font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                <span class="material-symbols-outlined text-xl">shopping_bag</span>
                Meus Pedidos
            </a>
            <a href="{{ route('customer.addresses') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm transition-colors rounded-xl {{ $active === 'addresses' ? 'font-bold text-primary bg-primary/5' : 'font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                <span class="material-symbols-outlined text-xl">location_on</span>
                Endereços
            </a>
            <a href="{{ route('customer.profile') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm transition-colors rounded-xl {{ $active === 'profile' ? 'font-bold text-primary bg-primary/5' : 'font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                <span class="material-symbols-outlined text-xl">person</span>
                Dados Pessoais
            </a>
            <hr class="my-2 border-slate-100 dark:border-slate-800" />
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium text-red-500 hover:bg-red-50 dark:hover:bg-red-950/20 rounded-xl transition-colors">
                    <span class="material-symbols-outlined text-xl">logout</span>
                    Sair
                </button>
            </form>
        </nav>
    </div>
</aside>