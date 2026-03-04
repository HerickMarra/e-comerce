<x-storefront-layout title="Categorias - Santo Lar">
    <main class="max-w-7xl mx-auto px-6 py-12 md:py-20 min-h-screen">
        <header class="mb-16">
            <h1
                class="text-4xl md:text-5xl font-extrabold text-slate-900 dark:text-white uppercase tracking-tighter mb-4">
                Categorias
            </h1>
            <p class="text-slate-500 max-w-xl text-lg">
                Explore todos os ambientes e peças curadas para transformar sua casa em um verdadeiro refúgio.
            </p>
        </header>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($categories as $category)
                <a href="{{ route('search', ['categories[]' => $category->id]) }}"
                    class="group relative aspect-[3/4] bg-slate-100 dark:bg-slate-900 rounded-[2.5rem] overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-700 border border-slate-100 dark:border-slate-800">

                    {{-- Image Background --}}
                    @if($category->image_path)
                        <img src="{{ asset('storage/' . $category->image_path) }}" alt="{{ $category->name }}"
                            class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
                    @else
                        <div class="absolute inset-0 flex items-center justify-center bg-slate-50 dark:bg-slate-800">
                            <span class="material-symbols-outlined text-6xl text-slate-200">category</span>
                        </div>
                    @endif

                    {{-- Gradient Overlay --}}
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/20 to-transparent transition-opacity duration-700 group-hover:opacity-100">
                    </div>

                    {{-- Content --}}
                    <div class="absolute inset-x-0 bottom-0 p-8 transform transition-transform duration-700">
                        <span
                            class="inline-block px-3 py-1 bg-primary/20 backdrop-blur-md text-primary text-[10px] font-bold uppercase tracking-widest rounded-full mb-3">
                            {{ $category->products_count }} {{ Str::plural('Produto', $category->products_count) }}
                        </span>
                        <h3
                            class="text-2xl md:text-3xl font-black text-white uppercase tracking-tighter leading-none mb-4 group-hover:translate-x-2 transition-transform duration-500">
                            {{ $category->name }}
                        </h3>

                        <div
                            class="flex items-center gap-2 text-white/70 text-xs font-bold uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-all duration-700 delay-100 translate-y-4 group-hover:translate-y-0">
                            <span>Explorar Coleção</span>
                            <span class="material-symbols-outlined !text-sm">arrow_forward</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        @if($categories->isEmpty())
            <div class="py-32 text-center">
                <span class="material-symbols-outlined text-7xl text-slate-200 mb-6">inventory_2</span>
                <p class="text-slate-400 font-medium">Nenhuma categoria encontrada no momento.</p>
            </div>
        @endif
    </main>
</x-storefront-layout>