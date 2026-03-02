<x-admin-layout>
    @section('page_title', 'Categorias')

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Categorias</h1>
            <p class="text-sm text-slate-500 mt-1">Organize seus produtos por categorias</p>
        </div>
        <a href="{{ route('admin.categories.create') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white text-sm font-bold rounded-xl shadow-lg shadow-primary/20 transition-all hover:scale-105">
            <span class="material-symbols-outlined text-lg">add</span>
            Nova Categoria
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-xl flex items-center gap-3 text-sm font-bold">
            <span class="material-symbols-outlined">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 rounded-xl text-sm font-bold">
            {{ $errors->first('message') }}
        </div>
    @endif

    {{-- Table --}}
    <div
        class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                    <th class="text-left px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">#
                    </th>
                    <th class="text-left px-4 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400 w-16">
                        IMG</th>
                    <th class="text-left px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Nome
                    </th>
                    <th class="text-left px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Slug
                    </th>
                    <th class="text-left px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                        Descrição</th>
                    <th class="text-center px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                        Produtos</th>
                    <th class="text-right px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                        Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                @forelse($categories as $category)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors group">
                        <td class="px-6 py-4 text-xs text-slate-400 font-mono">{{ $category->id }}</td>
                        <td class="px-4 py-4">
                            <div
                                class="size-10 rounded-lg bg-slate-100 dark:bg-slate-800 overflow-hidden border border-slate-100 dark:border-slate-800">
                                @if($category->image_path)
                                    <img src="{{ asset('storage/' . $category->image_path) }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-300">
                                        <span class="material-symbols-outlined text-sm">image</span>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-slate-900 dark:text-white">{{ $category->name }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <code
                                class="text-xs bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 px-2 py-1 rounded-lg">{{ $category->slug }}</code>
                        </td>
                        <td class="px-6 py-4 max-w-xs">
                            <span class="text-slate-500 text-xs truncate block">{{ $category->description ?: '—' }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span
                                class="inline-flex items-center justify-center w-8 h-8 rounded-xl text-xs font-black
                                            {{ $category->products_count > 0 ? 'bg-primary/10 text-primary' : 'bg-slate-100 text-slate-400' }}">
                                {{ $category->products_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div
                                class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('admin.categories.edit', $category) }}"
                                    class="p-2 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors text-slate-500 hover:text-slate-900 dark:hover:text-white"
                                    title="Editar">
                                    <span class="material-symbols-outlined text-lg">edit</span>
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" x-data
                                    @submit.prevent="if(confirm('Remover categoria \'{{ addslashes($category->name) }}\'?')) $el.submit()">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="p-2 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors text-slate-400 hover:text-red-500"
                                        title="Remover">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <span class="material-symbols-outlined text-5xl text-slate-200 block mb-3">category</span>
                            <p class="text-slate-400 text-sm font-medium">Nenhuma categoria cadastrada</p>
                            <a href="{{ route('admin.categories.create') }}"
                                class="mt-4 inline-flex items-center gap-1.5 text-primary text-sm font-bold hover:underline">
                                <span class="material-symbols-outlined text-base">add_circle</span> Criar primeira categoria
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($categories->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>