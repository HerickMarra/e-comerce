@props(['title', 'active' => 'dashboard'])

<x-storefront-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full">
        <nav aria-label="Breadcrumb" class="flex text-sm text-slate-500 mb-6 font-medium">
            <ol class="flex items-center space-x-2">
                <li><a class="hover:text-primary transition-colors" href="/">Home</a></li>
                <li><span class="material-symbols-outlined text-xs">chevron_right</span></li>
                <li><a class="hover:text-primary transition-colors" href="{{ route('customer.dashboard') }}">Minha
                        Conta</a></li>
                <li><span class="material-symbols-outlined text-xs">chevron_right</span></li>
                <li class="font-bold text-slate-900 dark:text-white uppercase tracking-tight">{{ $title }}</li>
            </ol>
        </nav>

        <div class="flex flex-col lg:flex-row gap-10">
            <x-customer-sidebar :active="$active" />

            <div class="flex-1">
                {{ $slot }}
            </div>
        </div>
    </div>
</x-storefront-layout>