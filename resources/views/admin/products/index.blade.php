<x-admin-layout>
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Produtos</h1>
                <p class="text-slate-500 mt-1">Gerencie seu catálogo de produtos e estoque.</p>
            </div>
            <a href="{{ route('admin.products.create') }}"
                class="inline-flex items-center justify-center px-5 py-3 bg-primary text-white font-semibold rounded-xl transition-all shadow-lg shadow-primary/20 gap-2">
                <span class="material-symbols-outlined text-xl">add</span>
                Novo Produto
            </a>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-emerald-500">check_circle</span>
                    <p class="text-emerald-700 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Products Table -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-50/50 border-b border-slate-100 uppercase text-[11px] font-bold text-slate-500 tracking-wider">
                            <th class="px-6 py-4">Produto</th>
                            <th class="px-6 py-4">Categorias</th>
                            <th class="px-6 py-4 text-right">Preço</th>
                            <th class="px-6 py-4 text-center">Estoque</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($products as $product)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center overflow-hidden border border-slate-200">
                                            @php $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->first(); @endphp
                                            @if($mainImage)
                                                <img src="{{ str_starts_with($mainImage->path, 'http') ? $mainImage->path : asset('storage/' . $mainImage->path) }}"
                                                    alt="{{ $product->name }}" class="w-full h-full object-cover">
                                            @else
                                                <span class="material-symbols-outlined text-slate-400">image</span>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900 group-hover:text-primary transition-colors">
                                                {{ $product->name }}
                                            </p>
                                            <p class="text-xs text-slate-500">ID: #{{ $product->id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($product->categories as $category)
                                            <span
                                                class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold">{{ $category->name }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-right font-bold text-slate-900">
                                    R$ {{ number_format($product->price, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span
                                        class="font-medium {{ $product->stock <= 5 ? 'text-amber-500' : 'text-slate-600' }}">
                                        {{ $product->stock }} un.
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span
                                        class="inline-flex px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $product->is_active ? 'bg-primary/10 text-primary' : 'bg-slate-100 text-slate-500' }}">
                                        {{ $product->is_active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.products.edit', $product) }}"
                                            class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-primary/10 hover:text-primary transition-all border border-slate-100">
                                            <span class="material-symbols-outlined text-xl">edit</span>
                                        </a>
                                        <button
                                            class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition-all border border-slate-100">
                                            <span class="material-symbols-outlined text-xl">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center">
                                            <span
                                                class="material-symbols-outlined text-4xl text-slate-300">inventory_2</span>
                                        </div>
                                        <h3 class="text-slate-900 font-bold">Nenhum produto cadastrado</h3>
                                        <p class="text-slate-500 text-sm">Comece adicionando seu primeiro produto ao
                                            catálogo.</p>
                                        <a href="{{ route('admin.products.create') }}"
                                            class="mt-2 text-emerald-600 font-bold hover:underline">Adicionar produto</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($products->hasPages())
                <div class="p-6 border-t border-slate-100 bg-slate-50/30">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>