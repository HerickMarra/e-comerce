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
                                        <button type="button"
                                            onclick="confirmDelete('{{ route('admin.products.destroy', $product) }}', '{{ $product->name }}')"
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

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background backdrop -->
            <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" aria-hidden="true"
                onclick="closeDeleteModal()"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div
                class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-8 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-14 w-14 rounded-2xl bg-red-50 sm:mx-0 sm:h-12 sm:w-12">
                            <span class="material-symbols-outlined text-red-500 text-2xl">delete_forever</span>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-6 sm:text-left">
                            <h3 class="text-xl leading-6 font-bold text-slate-900" id="modal-title">
                                Excluir Produto
                            </h3>
                            <div class="mt-3">
                                <p class="text-sm text-slate-500">
                                    Tem certeza que deseja excluir o produto <span id="productName"
                                        class="font-bold text-slate-900"></span>? Esta ação não pode ser desfeita.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50/50 px-4 py-4 sm:px-8 sm:flex sm:flex-row-reverse gap-3 mt-4">
                    <form id="deleteForm" method="POST" class="w-full sm:w-auto">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-lg shadow-red-500/20 px-6 py-3 bg-red-500 text-base font-bold text-white hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm transition-all">
                            Confirmar Exclusão
                        </button>
                    </form>
                    <button type="button" onclick="closeDeleteModal()"
                        class="mt-3 w-full sm:mt-0 sm:w-auto inline-flex justify-center rounded-xl border border-slate-200 shadow-sm px-6 py-3 bg-white text-base font-bold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:text-sm transition-all">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(url, name) {
            const modal = document.getElementById('deleteModal');
            const form = document.getElementById('deleteForm');
            const nameSpan = document.getElementById('productName');

            form.action = url;
            nameSpan.textContent = name;

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close on Escape key
        window.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeDeleteModal();
            }
        });
    </script>
</x-admin-layout>