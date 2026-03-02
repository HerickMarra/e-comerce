<x-admin-layout>
    @section('page_title', 'Nova Categoria')

    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.categories.index') }}"
            class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors text-slate-500">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Nova Categoria</h1>
            <p class="text-sm text-slate-500 mt-0.5">Adicione uma nova categoria ao catálogo</p>
        </div>
    </div>

    <div class="max-w-2xl">
        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data"
            class="space-y-6">
            @csrf

            <div
                class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm p-8 space-y-6">

                {{-- Nome --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 block" for="name">Nome
                        da Categoria <span class="text-red-500">*</span></label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                        placeholder="Ex: Sala de Estar"
                        class="w-full bg-slate-50 dark:bg-slate-800 border-2 {{ $errors->has('name') ? 'border-red-400' : 'border-slate-100 dark:border-slate-700' }} rounded-xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all">
                    @error('name')
                        <p class="text-xs text-red-500 font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Descrição --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 block"
                        for="description">Descrição <span class="text-slate-300">(opcional)</span></label>
                    <textarea id="description" name="description" rows="3" placeholder="Breve descrição da categoria..."
                        class="w-full bg-slate-50 dark:bg-slate-800 border-2 {{ $errors->has('description') ? 'border-red-400' : 'border-slate-100 dark:border-slate-700' }} rounded-xl px-5 py-4 text-sm font-medium focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all resize-none">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-xs text-red-500 font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-xl text-xs text-slate-400 flex items-start gap-2">
                    <span class="material-symbols-outlined text-sm text-slate-400 mt-0.5">info</span>
                    O <strong class="text-slate-600 dark:text-slate-300">slug</strong> será gerado automaticamente a
                    partir do nome.
                </div>

                {{-- Imagem --}}
                <div class="space-y-4" x-data="{ preview: null }">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 block">Imagem da
                        Categoria <span class="text-slate-300">(opcional)</span></label>
                    <div class="flex items-start gap-6">
                        <div
                            class="size-32 rounded-2xl bg-slate-50 dark:bg-slate-800 border-2 border-dashed border-slate-200 dark:border-slate-700 flex items-center justify-center overflow-hidden relative group">
                            <template x-if="preview">
                                <img :src="preview" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!preview">
                                <span class="material-symbols-outlined text-slate-300 text-3xl">image</span>
                            </template>
                        </div>
                        <div class="flex-1 space-y-3">
                            <input type="file" name="image" accept="image/*" class="hidden" id="category-image"
                                @change="const file = $event.target.files[0]; if(file) { const reader = new FileReader(); reader.onload = (e) => preview = e.target.result; reader.readAsDataURL(file); }">
                            <label for="category-image"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:border-primary transition-all cursor-pointer">
                                <span class="material-symbols-outlined text-sm">cloud_upload</span>
                                Selecionar Imagem
                            </label>
                            <p class="text-[10px] text-slate-400">Recomendado: 800x800px (JPG, PNG ou WebP). Máximo 2MB.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.categories.index') }}"
                    class="px-6 py-3 text-sm font-bold text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition-all">
                    Cancelar
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-8 py-3 bg-primary text-white text-sm font-bold rounded-xl shadow-lg shadow-primary/20 transition-all hover:scale-105">
                    <span class="material-symbols-outlined text-lg">save</span>
                    Criar Categoria
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>