<x-admin-layout>
    @section('page_title', 'Tênis & Calçados')

    <div class="space-y-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-primary/10 text-primary text-[10px] font-black uppercase tracking-wider mb-1">
                    <span class="material-symbols-outlined text-xs">steps</span>
                    Catálogo de Calçados
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">Tênis & Sneakers</h1>
                <p class="text-slate-500 text-xs sm:text-sm mt-0.5">Gerencie os modelos, numerações ativas, marcas e estoque por par.</p>
            </div>
            <a href="{{ route('admin.products.create') }}"
                class="inline-flex items-center justify-center px-5 sm:px-6 py-3 sm:py-3.5 bg-primary hover:opacity-95 text-white font-bold rounded-2xl transition-all shadow-lg shadow-primary/25 gap-2 text-xs sm:text-sm w-full sm:w-auto shrink-0">
                <span class="material-symbols-outlined text-xl">add</span>
                Cadastrar Novo Tênis
            </a>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 dark:bg-emerald-950/40 border-l-4 border-emerald-500 p-4 rounded-r-2xl">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-emerald-500">check_circle</span>
                    <p class="text-emerald-700 dark:text-emerald-300 font-bold text-sm">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Products Table -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-800 overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse min-w-[760px]">
                    <thead>
                        <tr class="bg-slate-50/75 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800 uppercase text-[10px] font-black text-slate-400 tracking-wider">
                            <th class="px-6 py-4">Tênis / Modelo</th>
                            <th class="px-6 py-4">Numerações (BR)</th>
                            <th class="px-6 py-4">Marca / Categorias</th>
                            <th class="px-6 py-4 text-right">Preço</th>
                            <th class="px-6 py-4 text-center">Estoque</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($products as $product)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors group">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden border border-slate-200 dark:border-slate-700 shrink-0">
                                            @php $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->first(); @endphp
                                            @if($mainImage)
                                                <img src="{{ str_starts_with($mainImage->path, 'http') ? $mainImage->path : asset('storage/' . $mainImage->path) }}"
                                                    alt="{{ $product->name }}" class="w-full h-full object-cover">
                                            @else
                                                <span class="material-symbols-outlined text-slate-400">steps</span>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                @if($product->brand)
                                                    <span class="text-[10px] font-black uppercase text-primary tracking-wider">{{ $product->brand }}</span>
                                                @endif
                                                @if($product->gender)
                                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-500 uppercase">{{ $product->gender }}</span>
                                                @endif
                                            </div>
                                            <p class="font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors text-sm truncate max-w-xs">
                                                {{ $product->name }}
                                            </p>
                                            @if($product->model_line)
                                                <p class="text-[11px] text-slate-400">{{ $product->model_line }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-5">
                                    @php $sizes = $product->sizes ?? []; @endphp
                                    @if(!empty($sizes) && count($sizes) > 0)
                                        <div class="flex flex-wrap items-center gap-1 max-w-[200px]">
                                            @foreach(array_slice($sizes, 0, 5) as $s)
                                                <span class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded text-[11px] font-bold">
                                                    {{ $s }}
                                                </span>
                                            @endforeach
                                            @if(count($sizes) > 5)
                                                <span class="text-[10px] font-bold text-slate-400">+{{ count($sizes) - 5 }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-[11px] text-slate-400 italic">Padrão Único</span>
                                    @endif
                                </td>

                                <td class="px-6 py-5">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($product->categories as $category)
                                            <span class="px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[10px] font-bold">
                                                {{ $category->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>

                                <td class="px-6 py-5 text-right">
                                    @if($product->hasDiscount())
                                        <span class="text-[10px] text-slate-400 line-through block">
                                            R$ {{ number_format($product->compare_price, 2, ',', '.') }}
                                        </span>
                                    @endif
                                    <span class="font-black text-slate-900 dark:text-white text-sm">
                                        R$ {{ number_format($product->price, 2, ',', '.') }}
                                    </span>
                                    @if($product->hasDiscount())
                                        <span class="inline-block text-[9px] font-black text-emerald-600 bg-emerald-50 dark:bg-emerald-950/40 px-1 rounded">
                                            -{{ $product->discountPercentage() }}%
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-5 text-center">
                                    <span class="font-bold text-xs {{ $product->stock <= 3 ? 'text-amber-500 font-black' : 'text-slate-700 dark:text-slate-300' }}">
                                        {{ $product->stock }} {{ $product->stock == 1 ? 'par' : 'pares' }}
                                    </span>
                                </td>

                                <td class="px-6 py-5 text-center">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wide {{ $product->is_active ? 'bg-primary/10 text-primary' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }}">
                                        {{ $product->is_active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>

                                <td class="px-6 py-5 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('product.show', $product->slug) }}" target="_blank"
                                            class="w-9 h-9 rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-400 flex items-center justify-center hover:bg-primary/10 hover:text-primary transition-all"
                                            title="Ver na loja">
                                            <span class="material-symbols-outlined text-lg">visibility</span>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product) }}"
                                            class="w-9 h-9 rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-400 flex items-center justify-center hover:bg-primary/10 hover:text-primary transition-all"
                                            title="Editar tênis">
                                            <span class="material-symbols-outlined text-lg">edit</span>
                                        </a>
                                        <button type="button"
                                            onclick="confirmDelete('{{ route('admin.products.destroy', $product) }}', '{{ $product->name }}')"
                                            class="w-9 h-9 rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-400 flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition-all"
                                            title="Excluir tênis">
                                            <span class="material-symbols-outlined text-lg">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center">
                                            <span class="material-symbols-outlined text-4xl text-slate-300">steps</span>
                                        </div>
                                        <h3 class="text-slate-900 dark:text-white font-bold">Nenhum tênis cadastrado ainda</h3>
                                        <p class="text-slate-500 text-sm">Adicione seu primeiro modelo de calçado para começar a vender.</p>
                                        <a href="{{ route('admin.products.create') }}"
                                            class="mt-2 inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white font-bold rounded-xl text-xs">
                                            <span class="material-symbols-outlined text-base">add</span>
                                            Cadastrar Primeiro Tênis
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($products->hasPages())
                <div class="p-6 border-t border-slate-100 dark:border-slate-800">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm hidden">
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 max-w-sm w-full space-y-6 shadow-2xl border border-slate-100 dark:border-slate-800">
            <div class="w-12 h-12 rounded-2xl bg-red-50 dark:bg-red-900/30 text-red-500 flex items-center justify-center mx-auto">
                <span class="material-symbols-outlined text-2xl">delete</span>
            </div>
            <div class="text-center space-y-2">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Excluir Tênis?</h3>
                <p class="text-slate-500 text-xs leading-relaxed">
                    Você tem certeza que deseja remover <strong id="deleteProductName" class="text-slate-900 dark:text-white"></strong>? Esta ação não pode ser desfeita.
                </p>
            </div>
            <form id="deleteForm" method="POST" class="grid grid-cols-2 gap-3">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeDeleteModal()"
                    class="py-3 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                    class="py-3 px-4 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs transition-colors shadow-lg shadow-red-600/30">
                    Sim, Excluir
                </button>
            </form>
        </div>
    </div>

    <script>
        function confirmDelete(url, name) {
            document.getElementById('deleteForm').action = url;
            document.getElementById('deleteProductName').innerText = name;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>
</x-admin-layout>
