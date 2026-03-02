<x-admin-layout>
    <div class="max-w-5xl mx-auto space-y-8">
        <!-- Header -->
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.products.index') }}"
                class="w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-all">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Novo Produto</h1>
                <p class="text-slate-500 mt-1">Cadastre seu produto com galeria de fotos reordenável.</p>
            </div>
        </div>

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6"
            x-data="productForm()" @submit.prevent="validateAndSubmit()">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Coluna Principal -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Informações Básicas -->
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 space-y-8">
                        <h2 class="text-xl font-bold text-slate-900 flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                                <span class="material-symbols-outlined">info</span>
                            </div>
                            Informações Básicas
                        </h2>

                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Nome do Produto</label>
                                <input type="text" name="name" value="{{ old('name') }}"
                                    class="w-full bg-slate-50 border-slate-200 rounded-2xl focus:ring-primary/50 focus:border-primary transition-all font-medium py-4 px-6 border-2"
                                    placeholder="Ex: Sofá Chesterfield Couro">
                                @error('name') <p class="mt-2 text-sm text-red-500 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Descrição Detalhada</label>
                                <textarea name="description" rows="6"
                                    class="w-full bg-slate-50 border-slate-200 rounded-2xl focus:ring-primary/50 focus:border-primary transition-all font-medium py-4 px-6 border-2"
                                    placeholder="Descreva os materiais, dimensões e diferenciais...">{{ old('description') }}</textarea>
                                @error('description') <p class="mt-2 text-sm text-red-500 font-medium">{{ $message }}
                                </p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Especificações
                                    Técnicas</label>
                                <textarea name="technical_specifications" rows="6"
                                    class="w-full bg-slate-50 border-slate-200 rounded-2xl focus:ring-primary/50 focus:border-primary transition-all font-medium py-4 px-6 border-2"
                                    placeholder="Poderá colocar especificações técnicas aqui...">{{ old('technical_specifications') }}</textarea>
                                @error('technical_specifications') <p class="mt-2 text-sm text-red-500 font-medium">
                                    {{ $message }}
                                </p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Cores (Move to left) -->
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 space-y-8">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold text-slate-900 flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                                    <span class="material-symbols-outlined">palette</span>
                                </div>
                                Opções de Cores
                            </h2>
                            <button type="button" @click="addColor()"
                                class="text-sm font-bold text-purple-600 hover:text-purple-700 flex items-center gap-1 transition-colors">
                                <span class="material-symbols-outlined text-base">add_circle</span>
                                Adicionar Cor
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4" x-show="colors.length > 0">
                            <template x-for="(colorObj, index) in colors" :key="index">
                                <div
                                    class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100 group transition-all hover:bg-white hover:shadow-lg hover:shadow-slate-200/50">
                                    <!-- Color Picker Preview -->
                                    <div
                                        class="relative w-12 h-12 rounded-xl overflow-hidden shadow-inner border-2 border-white ring-1 ring-slate-200 shrink-0">
                                        <input type="color" x-model="colors[index].hex"
                                            class="absolute inset-[-50%] w-[200%] h-[200%] cursor-pointer">
                                    </div>

                                    <div class="flex-1 space-y-1">
                                        <input type="text" x-model="colors[index].name" :name="`colors[${index}][name]`"
                                            class="w-full bg-transparent border-none p-0 focus:ring-0 font-bold text-sm text-slate-700 placeholder:text-slate-400"
                                            placeholder="Nome da Cor">
                                        <input type="text" x-model="colors[index].hex" :name="`colors[${index}][hex]`"
                                            class="w-full bg-transparent border-none p-0 focus:ring-0 font-mono text-[10px] uppercase text-slate-400"
                                            placeholder="#FFFFFF">
                                    </div>

                                    <button type="button" @click="removeColor(index)"
                                        class="w-8 h-8 rounded-lg bg-white text-slate-300 hover:text-red-500 hover:bg-red-50 transition-all flex items-center justify-center opacity-0 group-hover:opacity-100">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <div x-show="colors.length === 0"
                            class="py-10 text-center border-2 border-dashed border-slate-100 rounded-3xl">
                            <span class="material-symbols-outlined text-slate-200 text-5xl mb-2">palette</span>
                            <p class="text-sm text-slate-400 font-medium">Defina as opções de cores para este produto.
                            </p>
                        </div>
                    </div>

                    <!-- Galeria de Imagens -->
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 space-y-8">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold text-slate-900 flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                                    <span class="material-symbols-outlined">imagesmode</span>
                                </div>
                                Galeria de Imagens
                            </h2>
                            <span
                                class="text-xs font-bold text-slate-400 bg-slate-50 px-3 py-1.5 rounded-full uppercase tracking-wider"
                                x-text="`${images.length} fotos` text-sm"></span>
                        </div>

                        <!-- Upload Area -->
                        <div class="relative">
                            <input type="file" multiple accept="image/*"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20"
                                @change="handleFileUpload($event)">
                            <div
                                class="w-full py-12 border-2 border-dashed border-slate-200 rounded-3xl flex flex-col items-center justify-center hover:border-emerald-500 transition-all bg-slate-50/50 group">
                                <div
                                    class="w-16 h-16 bg-white rounded-2xl shadow-sm border border-slate-100 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                    <span
                                        class="material-symbols-outlined text-emerald-500 text-3xl">add_photo_alternate</span>
                                </div>
                                <p class="text-base font-bold text-slate-700">Clique ou arraste imagens aqui</p>
                                <p class="text-sm text-slate-400 mt-1">A primeira imagem será a principal</p>
                            </div>
                        </div>

                        <!-- Sortable Gallery -->
                        <div x-ref="sortableContainer"
                            class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 min-h-[140px]">
                            <template x-for="(img, index) in images" :key="img.id">
                                <div :data-id="img.id"
                                    class="relative aspect-square rounded-2xl border-2 border-slate-100 overflow-hidden bg-slate-50 cursor-move group hover:border-emerald-500 transition-all shadow-sm">
                                    <img :src="img.preview" class="w-full h-full object-cover">

                                    <!-- Badges -->
                                    <div class="absolute top-2 left-2 flex flex-col gap-1">
                                        <template x-if="index === 0">
                                            <span
                                                class="px-2 py-0.5 bg-emerald-600 text-[9px] font-black text-white uppercase rounded-lg shadow-lg">Principal</span>
                                        </template>
                                        <span
                                            class="w-6 h-6 flex items-center justify-center bg-slate-900/60 backdrop-blur-md text-white rounded-lg text-[10px] font-black"
                                            x-text="index + 1"></span>
                                    </div>

                                    <!-- Actions -->
                                    <div
                                        class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                        <button type="button" @click="removeImage(index)"
                                            class="w-10 h-10 bg-white text-red-500 rounded-xl hover:bg-red-50 transition-colors shadow-xl">
                                            <span class="material-symbols-outlined text-xl">delete</span>
                                        </button>
                                    </div>

                                    <!-- Hidden Inputs -->
                                    <input type="hidden" :name="`image_order[${img.id}]`" :value="index">
                                    <template x-if="img.type === 'url'">
                                        <input type="hidden" name="image_urls[]" :value="img.preview">
                                    </template>
                                </div>
                            </template>
                        </div>

                        <!-- Real file inputs container (hidden) -->
                        <div class="hidden" x-ref="fileInputs"></div>

                        <!-- External URL Option -->
                        <div class="pt-6 border-t border-slate-100">
                            <label class="block text-sm font-bold text-slate-500 mb-3 flex items-center gap-2">
                                <span class="material-symbols-outlined text-base">link</span>
                                Adicionar por URL externa
                            </label>
                            <div class="flex gap-2">
                                <input type="url" x-model="externalUrl"
                                    class="flex-1 bg-slate-50 border-slate-200 rounded-xl focus:ring-primary-500 transition-all font-medium py-3 px-4"
                                    placeholder="https://images.unsplash.com/...">
                                <button type="button" @click="addExternalImage()"
                                    class="px-6 py-3 bg-slate-900 text-white font-bold rounded-xl hover:bg-slate-800 transition-all">Adicionar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Coluna Lateral -->
                <div class="space-y-6">
                    <!-- Preço e Estoque -->
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 space-y-8">
                        <h2 class="text-xl font-bold text-slate-900 flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                                <span class="material-symbols-outlined">payments</span>
                            </div>
                            Valores
                        </h2>

                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Preço de Venda (R$)</label>
                                <div class="relative">
                                    <span
                                        class="absolute left-6 top-1/2 -translate-y-1/2 font-bold text-slate-400">R$</span>
                                    <input type="number" step="0.01" name="price" value="{{ old('price') }}"
                                        class="w-full bg-slate-50 border-slate-200 rounded-2xl focus:ring-emerald-500 focus:border-emerald-500 transition-all font-black py-4 pl-14 pr-6 border-2 text-xl"
                                        placeholder="0,00">
                                </div>
                                @error('price') <p class="mt-2 text-sm text-red-500 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Estoque Inicial</label>
                                <input type="number" name="stock" value="{{ old('stock', 0) }}"
                                    class="w-full bg-slate-50 border-slate-200 rounded-2xl focus:ring-emerald-500 focus:border-emerald-500 transition-all font-black py-4 px-6 border-2"
                                    placeholder="0">
                                @error('stock') <p class="mt-2 text-sm text-red-500 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Dimensões para cálculo de frete --}}
                            <div class="pt-2 border-t border-slate-100">
                                <p class="text-sm font-bold text-slate-700 mb-1">Dimensões para Frete</p>
                                <p class="text-[11px] text-slate-400 mb-4">Obrigatório para cálculo automático via
                                    EnviaMais.</p>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1.5">Peso (kg)</label>
                                        <input type="number" step="0.001" name="weight" value="{{ old('weight') }}"
                                            class="w-full bg-slate-50 border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 transition-all py-3 px-4 border-2 text-sm"
                                            placeholder="0.000">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1.5">Altura (cm)</label>
                                        <input type="number" step="0.01" name="height" value="{{ old('height') }}"
                                            class="w-full bg-slate-50 border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 transition-all py-3 px-4 border-2 text-sm"
                                            placeholder="0.00">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1.5">Largura
                                            (cm)</label>
                                        <input type="number" step="0.01" name="width" value="{{ old('width') }}"
                                            class="w-full bg-slate-50 border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 transition-all py-3 px-4 border-2 text-sm"
                                            placeholder="0.00">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 mb-1.5">Comprimento
                                            (cm)</label>
                                        <input type="number" step="0.01" name="length" value="{{ old('length') }}"
                                            class="w-full bg-slate-50 border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500 transition-all py-3 px-4 border-2 text-sm"
                                            placeholder="0.00">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Categorias -->
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 space-y-8">
                        <h2 class="text-xl font-bold text-slate-900 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                <span class="material-symbols-outlined">sell</span>
                            </div>
                            Categorias
                        </h2>

                        <div>
                            <select name="categories[]" multiple
                                class="w-full bg-slate-50 border-slate-200 rounded-2xl focus:ring-emerald-500 focus:border-emerald-500 transition-all font-medium py-4 px-6 border-2 h-48 custom-scrollbar">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ in_array($category->id, old('categories', [])) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-3 text-[11px] text-slate-400 font-bold uppercase tracking-tight">Segure Ctrl
                                para selecionar mais de uma</p>
                            @error('categories') <p class="mt-2 text-sm text-red-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Botões de Ação -->
                    <div class="pt-4 flex flex-col gap-3">
                        <button type="submit"
                            class="w-full py-5 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-lg rounded-3xl transition-all shadow-2xl shadow-emerald-200 flex items-center justify-center gap-3">
                            <span class="material-symbols-outlined">cloud_done</span>
                            Salvar Produto
                        </button>
                        <a href="{{ route('admin.products.index') }}"
                            class="w-full py-5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-3xl transition-all text-center">
                            Descartar
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
        <script>
            function productForm() {
                return {
                    images: [],
                    colors: [],
                    externalUrl: '',
                    fileCount: 0,
                    nextId: 1,

                    addColor() {
                        this.colors.push({ hex: '#10B981', name: '' });
                    },

                    removeColor(index) {
                        this.colors.splice(index, 1);
                    },

                    init() {
                        this.$nextTick(() => {
                            new Sortable(this.$refs.sortableContainer, {
                                animation: 150,
                                handle: '.group',
                                onEnd: (evt) => {
                                    const movedItem = this.images.splice(evt.oldIndex, 1)[0];
                                    this.images.splice(evt.newIndex, 0, movedItem);
                                    this.syncFileInputs();
                                }
                            });
                        });
                    },

                    handleFileUpload(event) {
                        const files = Array.from(event.target.files);
                        files.forEach(file => {
                            const reader = new FileReader();
                            const id = 'file_' + (++this.fileCount);

                            reader.onload = (e) => {
                                this.images.push({
                                    id: id,
                                    file: file,
                                    preview: e.target.result,
                                    type: 'file'
                                });
                                this.addFileInput(id, file);
                            };
                            reader.readAsDataURL(file);
                        });
                        event.target.value = ''; // Reset input
                    },

                    addFileInput(id, file) {
                        const input = document.createElement('input');
                        input.type = 'file';
                        input.name = 'images[]';
                        input.id = 'input_' + id;

                        const container = new DataTransfer();
                        container.items.add(file);
                        input.files = container.files;

                        this.$refs.fileInputs.appendChild(input);
                    },

                    syncFileInputs() {
                        // This is complex because we can't easily reorder files in the DOM array
                        // But our controller uses image_order mapping, so we just need them all to be present
                    },

                    addExternalImage() {
                        if (this.externalUrl) {
                            this.images.push({
                                id: 'ext_' + (this.nextId++),
                                preview: this.externalUrl,
                                type: 'url'
                            });
                            this.externalUrl = '';
                        }
                    },

                    removeImage(index) {
                        const img = this.images[index];
                        if (img.type === 'file') {
                            const input = document.getElementById('input_' + img.id);
                            if (input) input.remove();
                        }
                        this.images.splice(index, 1);
                    },

                    validateAndSubmit() {
                        const form = this.$el;
                        const formData = new FormData(form);

                        let errors = [];

                        if (!formData.get('name')) errors.push("O nome do produto é obrigatório.");
                        if (!formData.get('price')) errors.push("O preço é obrigatório.");
                        if (!formData.get('stock')) errors.push("O estoque é obrigatório.");
                        if (formData.getAll('categories[]').length === 0) errors.push("Selecione pelo menos uma categoria.");
                        if (this.images.length === 0) errors.push("Adicione pelo menos uma imagem.");

                        if (errors.length > 0) {
                            alert("Por favor, preencha os campos obrigatórios:\n\n- " + errors.join("\n- "));
                            return;
                        }

                        form.submit();
                    }
                }
            }
        </script>
        <style>
            .custom-scrollbar::-webkit-scrollbar {
                width: 6px;
            }

            .custom-scrollbar::-webkit-scrollbar-track {
                background: transparent;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #e2e8f0;
                border-radius: 10px;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #cbd5e1;
            }
        </style>
    @endpush
</x-admin-layout>