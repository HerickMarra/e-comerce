<x-admin-layout>
    @section('page_title', 'Cadastrar Novo Produto')

    <div class="max-w-6xl mx-auto space-y-8" x-data="productForm()">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.products.index') }}"
                    class="w-10 h-10 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-white transition-all">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-primary/10 text-primary text-[10px] font-black uppercase tracking-wider mb-1">
                        <span class="material-symbols-outlined text-xs">inventory_2</span>
                        Catálogo Geral
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">Cadastrar Novo Produto</h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Cadastre roupas, calçados, airsoft, barracas, equipamentos e acessórios.</p>
                </div>
            </div>
        </div>

        <form id="productFormTag" x-ref="productFormRef" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8" @submit.prevent="validateAndSubmit($event)">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Coluna Principal (2/3) -->
                <div class="lg:col-span-2 space-y-8">

                    <!-- CARD 1: Dados Principais do Produto -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-100 dark:border-slate-800 space-y-6">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-3 border-b border-slate-100 dark:border-slate-800 pb-4">
                            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                                <span class="material-symbols-outlined">inventory_2</span>
                            </div>
                            Informações Principais do Produto
                        </h2>

                        <div class="space-y-5">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-2">
                                    Nome do Produto <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-primary focus:border-primary transition-all font-bold py-3.5 px-5 border-2 text-base text-slate-900 dark:text-white"
                                    placeholder="Ex: Rifle Airsoft M4A1, Barraca Indy 4P, Camiseta Dry Fit, Tênis Pegasus 40...">
                                @error('name') <p class="mt-1.5 text-xs text-red-500 font-bold">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-2">
                                        Marca / Fabricante
                                    </label>
                                    <div class="relative">
                                        <input type="text" name="brand" x-model="brand" list="brands-list"
                                            class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-primary focus:border-primary transition-all font-semibold py-3 px-4 border-2 text-sm text-slate-900 dark:text-white"
                                            placeholder="Ex: Cyma, Rossi, Nautika, Shimano, Invictus, Nike...">
                                        <datalist id="brands-list">
                                            <option value="Cyma">
                                            <option value="Rossi">
                                            <option value="Nautika">
                                            <option value="Shimano">
                                            <option value="Invictus">
                                            <option value="Coleman">
                                            <option value="Guepardo">
                                            <option value="Marine Sports">
                                            <option value="Daiwa">
                                            <option value="Taurus">
                                            <option value="Nike">
                                            <option value="Adidas">
                                            <option value="Asics">
                                            <option value="Olympikus">
                                        </datalist>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-2">
                                        Linha / Coleção / Categoria
                                    </label>
                                    <input type="text" name="model_line" value="{{ old('model_line') }}"
                                        class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-primary focus:border-primary transition-all font-semibold py-3 px-4 border-2 text-sm text-slate-900 dark:text-white"
                                        placeholder="Ex: Tático / Aventura / Alta Performance">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-2">
                                    Gênero / Público-Alvo
                                </label>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    @foreach(['Unissex', 'Masculino', 'Feminino', 'Infantil'] as $genderOption)
                                        <label class="flex items-center justify-center gap-2 p-3 rounded-xl border-2 cursor-pointer transition-all text-xs font-bold"
                                            :class="gender === '{{ $genderOption }}' ? 'border-primary bg-primary/10 text-primary' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:border-slate-300'">
                                            <input type="radio" name="gender" value="{{ $genderOption }}" class="hidden" x-model="gender">
                                            <span>{{ $genderOption }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-2">
                                    Descrição e Detalhes do Produto
                                </label>
                                <textarea name="description" rows="5"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-primary focus:border-primary transition-all font-medium p-4 border-2 text-sm text-slate-900 dark:text-white"
                                    placeholder="Descreva as especificações, materiais, recursos, utilidade e diferenciais do produto...">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- CARD 2: Grade de Variações e Tamanhos (Universal) -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-100 dark:border-slate-800 space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-4">
                            <div>
                                <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 flex items-center justify-center">
                                        <span class="material-symbols-outlined">dashboard_customize</span>
                                    </div>
                                    Grade de Tamanhos & Variações
                                </h2>
                                <p class="text-xs text-slate-400 mt-1">Defina as opções disponíveis (tamanhos de roupa, calçados, calibres, gramaturas ou tamanho único).</p>
                            </div>
                            <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 rounded-full text-xs font-black text-slate-700 dark:text-slate-300 w-fit">
                                <span x-text="selectedSizes.length"></span> variações ativas
                            </span>
                        </div>

                        {{-- Botões de Predefinições Rápidas por Nicho --}}
                        <div class="space-y-2">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Predefinições Rápidas:</span>
                            <div class="flex flex-wrap items-center gap-2">
                                <button type="button" @click="setSizes(['PP','P','M','G','GG','XG','XXG'])"
                                    class="px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs font-bold transition-all flex items-center gap-1.5">
                                    <span>👕</span> Roupas (PP ao XXG)
                                </button>
                                <button type="button" @click="setSizes(['36','37','38','39','40','41','42','43','44'])"
                                    class="px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200 text-xs font-bold transition-all flex items-center gap-1.5">
                                    <span>👟</span> Calçados (36 ao 44)
                                </button>
                                <button type="button" @click="setSizes(['Único'])"
                                    class="px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-200 text-xs font-bold transition-all flex items-center gap-1.5">
                                    <span>📦</span> Tamanho Único
                                </button>
                                <button type="button" @click="setSizes(['0.20g','0.25g','0.28g','6mm'])"
                                    class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold transition-all flex items-center gap-1.5">
                                    <span>🎯</span> Airsoft (Gramaturas)
                                </button>
                                <button type="button" @click="setSizes(['Manivela Direita','Manivela Esquerda'])"
                                    class="px-3 py-1.5 rounded-xl bg-cyan-50 hover:bg-cyan-100 dark:bg-cyan-900/30 text-cyan-800 dark:text-cyan-200 text-xs font-bold transition-all flex items-center gap-1.5">
                                    <span>🎣</span> Pesca (Manivelas)
                                </button>
                                <button type="button" @click="selectedSizes = []"
                                    class="px-3 py-1.5 rounded-xl bg-red-50 hover:bg-red-100 dark:bg-red-950/40 text-red-600 dark:text-red-400 text-xs font-bold transition-all">
                                    Limpar
                                </button>
                            </div>
                        </div>

                        {{-- Lista Visual de Variações Ativas Selecionadas --}}
                        <div x-show="selectedSizes.length > 0" class="p-4 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-100 dark:border-slate-800 space-y-2">
                            <span class="text-[11px] font-bold text-slate-500 block uppercase tracking-wider">Variações selecionadas para a matriz de estoque:</span>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="size in selectedSizes" :key="'badge_' + size">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900 text-white dark:bg-white dark:text-slate-900 font-bold text-xs shadow-sm">
                                        <span x-text="size"></span>
                                        <button type="button" @click="toggleSize(size)" class="hover:text-red-400 text-sm font-black leading-none ml-1" title="Remover variação">×</button>
                                    </span>
                                </template>
                            </div>
                        </div>

                        {{-- Grade de Variações Comuns Clicáveis --}}
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wider mb-2">Clique para ativar ou desativar opções comuns:</span>
                            <div class="grid grid-cols-3 xs:grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-2">
                                <template x-for="size in allSizes" :key="size">
                                    <button type="button" @click="toggleSize(size)"
                                        :class="selectedSizes.includes(size) 
                                            ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 border-slate-900 dark:border-white shadow-md ring-2 ring-primary/40' 
                                            : 'bg-slate-50 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:border-slate-400'"
                                        class="py-2 px-1 rounded-xl text-center font-bold text-xs border transition-all hover:scale-105 active:scale-95 flex items-center justify-center">
                                        <span x-text="size"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        {{-- Adicionar Variação / Tamanho Personalizado --}}
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <input type="text" x-model="customSizeInput" @keydown.enter.prevent="addCustomSize()"
                                placeholder="Digite outro tamanho ou especificação (ex: XG, 3000mm, 6mm, 4 Pessoas...)"
                                class="w-full sm:flex-1 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs font-bold focus:ring-primary focus:border-primary border text-slate-900 dark:text-white">
                            <button type="button" @click="addCustomSize()"
                                class="w-full sm:w-auto px-5 py-2.5 bg-slate-900 dark:bg-slate-700 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition-colors flex items-center justify-center gap-1.5">
                                <span class="material-symbols-outlined text-base">add</span>
                                <span>Adicionar Opção</span>
                            </button>
                        </div>

                        {{-- Hidden inputs for form submit --}}
                        <template x-for="size in selectedSizes" :key="'hidden_' + size">
                            <input type="hidden" name="sizes[]" :value="size">
                        </template>
                    </div>

                    <!-- CARD 3: Cores & Padrões Visuais -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-100 dark:border-slate-800 space-y-6">
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                            <div>
                                <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 flex items-center justify-center">
                                        <span class="material-symbols-outlined">palette</span>
                                    </div>
                                    Cores & Padrões Visuais
                                </h2>
                                <p class="text-xs text-slate-400 mt-1">Cadastre as opções de cores ou camuflagens disponíveis deste produto.</p>
                            </div>
                            <button type="button" @click="addColor()"
                                class="text-xs font-bold text-purple-600 hover:text-purple-700 bg-purple-50 dark:bg-purple-900/30 px-3 py-2 rounded-xl flex items-center gap-1 transition-colors">
                                <span class="material-symbols-outlined text-base">add</span>
                                Nova Cor
                            </button>
                        </div>

                        {{-- Presets de cores populares --}}
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Cores & Padrões Populares:</span>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" @click="addPresetColor('#000000', 'Preto (Black)')"
                                    class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-black text-white text-xs font-bold hover:scale-105 transition-all flex items-center gap-1.5">
                                    <span class="size-2 rounded-full bg-white"></span> Preto
                                </button>
                                <button type="button" @click="addPresetColor('#C2A676', 'Tan / Coyote')"
                                    class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-[#C2A676] text-slate-900 text-xs font-bold hover:scale-105 transition-all flex items-center gap-1.5">
                                    <span class="size-2 rounded-full bg-white"></span> Tan / Coyote
                                </button>
                                <button type="button" @click="addPresetColor('#556B2F', 'Verde Oliva / Militar')"
                                    class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-[#556B2F] text-white text-xs font-bold hover:scale-105 transition-all flex items-center gap-1.5">
                                    <span class="size-2 rounded-full bg-white"></span> Verde Oliva
                                </button>
                                <button type="button" @click="addPresetColor('#6B705C', 'Camuflado Multicam')"
                                    class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-[#6B705C] text-white text-xs font-bold hover:scale-105 transition-all flex items-center gap-1.5">
                                    <span class="size-2 rounded-full bg-white"></span> Multicam
                                </button>
                                <button type="button" @click="addPresetColor('#FFFFFF', 'Branco (White)')"
                                    class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white text-slate-900 text-xs font-bold hover:scale-105 transition-all flex items-center gap-1.5">
                                    <span class="size-2 rounded-full bg-slate-300"></span> Branco
                                </button>
                                <button type="button" @click="addPresetColor('#808080', 'Cinza / Chumbo')"
                                    class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-500 text-white text-xs font-bold hover:scale-105 transition-all flex items-center gap-1.5">
                                    <span class="size-2 rounded-full bg-white"></span> Cinza
                                </button>
                                <button type="button" @click="addPresetColor('#1E3A8A', 'Azul Marinho')"
                                    class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-blue-900 text-white text-xs font-bold hover:scale-105 transition-all flex items-center gap-1.5">
                                    <span class="size-2 rounded-full bg-white"></span> Azul Marinho
                                </button>
                                <button type="button" @click="addPresetColor('#EA580C', 'Laranja Resgate')"
                                    class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-orange-600 text-white text-xs font-bold hover:scale-105 transition-all flex items-center gap-1.5">
                                    <span class="size-2 rounded-full bg-white"></span> Laranja
                                </button>
                            </div>
                        </div>
                                </button>
                                <button type="button" @click="addPresetColor('#DC2626', 'Vermelho')"
                                    class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-red-600 text-white text-xs font-bold hover:scale-105 transition-all flex items-center gap-1.5">
                                    <span class="size-2 rounded-full bg-white"></span> Vermelho
                                </button>
                                <button type="button" @click="addPresetColor('#F5F5DC', 'Off-White / Gelo')"
                                    class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-amber-50 text-amber-900 text-xs font-bold hover:scale-105 transition-all flex items-center gap-1.5">
                                    <span class="size-2 rounded-full bg-amber-200"></span> Off-White
                                </button>
                            </div>
                        </div>

                        {{-- Lista de Cores Configuradas --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <template x-for="(colorObj, index) in colors" :key="index">
                                <div class="flex items-center gap-3 p-3.5 bg-slate-50 dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 transition-all">
                                    <div class="relative w-10 h-10 rounded-xl overflow-hidden shadow-inner border border-slate-300 dark:border-slate-600 shrink-0">
                                        <input type="color" x-model="colors[index].hex"
                                            class="absolute inset-[-50%] w-[200%] h-[200%] cursor-pointer">
                                    </div>
                                    <div class="flex-1 space-y-0.5">
                                        <input type="text" x-model="colors[index].name" :name="`colors[${index}][name]`"
                                            class="w-full bg-transparent border-none p-0 focus:ring-0 font-bold text-xs text-slate-800 dark:text-slate-200 placeholder:text-slate-400"
                                            placeholder="Nome da cor (ex: Triple Black)">
                                        <input type="text" x-model="colors[index].hex" :name="`colors[${index}][hex]`"
                                            class="w-full bg-transparent border-none p-0 focus:ring-0 font-mono text-[10px] uppercase text-slate-400">
                                    </div>
                                    <button type="button" @click="removeColor(index)"
                                        class="p-1.5 text-slate-400 hover:text-red-500 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                        <span class="material-symbols-outlined text-base">close</span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- CARD: Controle de Estoque por Variação (Tamanho x Cor) -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-100 dark:border-slate-800 space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-4">
                            <div>
                                <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 flex items-center justify-center">
                                        <span class="material-symbols-outlined">grid_view</span>
                                    </div>
                                    Controle de Estoque por Variação
                                </h2>
                                <p class="text-xs text-slate-400 mt-1">Gerencie a quantidade individual de pares disponíveis para cada combinação de Cor e Tamanho.</p>
                            </div>
                            
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <div class="px-3.5 py-1.5 rounded-full bg-primary/10 border border-primary/20 text-primary text-xs font-black flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-sm">inventory_2</span>
                                    <span>Estoque Total: <strong x-text="totalMatrixStock"></strong> pares</span>
                                </div>
                                <template x-if="totalOutOfStock > 0">
                                    <span class="px-2.5 py-1 rounded-full bg-red-50 text-red-600 dark:bg-red-950/40 dark:text-red-400 text-[11px] font-bold">
                                        <span x-text="totalOutOfStock"></span> esgotadas
                                    </span>
                                </template>
                                <template x-if="totalLowStock > 0">
                                    <span class="px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 text-[11px] font-bold">
                                        <span x-text="totalLowStock"></span> baixo estoque
                                    </span>
                                </template>
                            </div>
                        </div>

                        <!-- Barra de Ferramentas / Preenchimento Rápido -->
                        <div class="flex flex-wrap items-center justify-between gap-3 p-3 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-200/80 dark:border-slate-700/80 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">Ações Rápidas:</span>
                                <button type="button" @click="bulkFillMatrixPrompt()"
                                    class="px-2.5 py-1 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 hover:border-primary text-slate-700 dark:text-slate-200 rounded-lg font-bold transition-all flex items-center gap-1">
                                    <span class="material-symbols-outlined text-xs">edit_note</span>
                                    Preencher Todos
                                </button>
                                <button type="button" @click="clearAllMatrixStock()"
                                    class="px-2.5 py-1 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 hover:border-red-400 text-red-600 rounded-lg font-bold transition-all flex items-center gap-1">
                                    <span class="material-symbols-outlined text-xs">delete_sweep</span>
                                    Zerar Matriz
                                </button>
                            </div>

                            <div class="flex items-center gap-3 text-[11px] text-slate-400">
                                <span class="flex items-center gap-1"><span class="size-2 rounded-full bg-red-500"></span> 0 = Esgotado</span>
                                <span class="flex items-center gap-1"><span class="size-2 rounded-full bg-amber-500"></span> 1 a 3 = Baixo</span>
                                <span class="flex items-center gap-1"><span class="size-2 rounded-full bg-emerald-500"></span> 4+ = Disponível</span>
                            </div>
                        </div>

                        <!-- Matriz Vazia Alerta -->
                        <template x-if="selectedSizes.length === 0 || colors.length === 0">
                            <div class="text-center py-8 px-4 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-2xl">
                                <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">warning</span>
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-300">Selecione pelo menos 1 tamanho e adicione 1 cor acima para gerar a matriz de estoque.</p>
                            </div>
                        </template>

                        <!-- Tabela Matriz Interativa -->
                        <div x-show="selectedSizes.length > 0 && colors.length > 0" class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="bg-slate-100/80 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-b border-slate-200 dark:border-slate-700">
                                        <th class="py-3 px-4 font-black uppercase tracking-wider text-[11px] min-w-[200px]">Cor / Colorway</th>
                                        <template x-for="size in selectedSizes" :key="'col_size_' + size">
                                            <th class="py-3 px-2 font-black text-center text-slate-900 dark:text-white uppercase tracking-wider min-w-[75px]">
                                                <div class="flex flex-col items-center">
                                                    <span class="text-sm" x-text="size"></span>
                                                    <span class="text-[9px] text-slate-400 font-bold">BR</span>
                                                </div>
                                            </th>
                                        </template>
                                        <th class="py-3 px-4 font-black text-right uppercase tracking-wider text-[11px] min-w-[90px]">Total Cor</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-slate-900">
                                    <template x-for="(colorObj, cIndex) in colors" :key="'row_color_' + cIndex">
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
                                            <td class="py-3 px-4 font-bold text-slate-800 dark:text-slate-200">
                                                <div class="flex items-center gap-2.5">
                                                    <div class="size-6 rounded-lg border border-slate-300 dark:border-slate-600 shadow-sm shrink-0"
                                                         :style="`background-color: ${colorObj.hex}`"></div>
                                                    <div class="truncate max-w-[170px]">
                                                        <span class="block font-black text-xs truncate" x-text="colorObj.name || ('Cor ' + (cIndex + 1))"></span>
                                                        <span class="block font-mono text-[9px] uppercase text-slate-400" x-text="colorObj.hex"></span>
                                                    </div>
                                                </div>
                                            </td>

                                            <template x-for="size in selectedSizes" :key="'cell_' + cIndex + '_' + size">
                                                <td class="py-2.5 px-1.5 text-center">
                                                    <div class="relative inline-block w-full max-w-[70px]">
                                                        <input type="number" min="0" step="1"
                                                            :name="`matrix[${cIndex}][${size}]`"
                                                            :value="getMatrixStock(cIndex, size)"
                                                            @input="setMatrixStock(cIndex, size, $event.target.value)"
                                                            class="w-full text-center font-black py-2 px-1 rounded-xl text-xs border transition-all focus:ring-2 focus:ring-primary focus:border-primary"
                                                            :class="{
                                                                'bg-red-50/60 dark:bg-red-950/30 border-red-300 dark:border-red-900 text-red-600 dark:text-red-400': isOutOfStock(cIndex, size),
                                                                'bg-amber-50/60 dark:bg-amber-950/30 border-amber-300 dark:border-amber-800 text-amber-700 dark:text-amber-400': hasLowStock(cIndex, size),
                                                                'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white': !isOutOfStock(cIndex, size) && !hasLowStock(cIndex, size)
                                                            }">
                                                        <template x-if="isOutOfStock(cIndex, size)">
                                                            <span class="text-[8px] font-black uppercase text-red-500 block -mt-0.5">Esgotado</span>
                                                        </template>
                                                        <template x-if="hasLowStock(cIndex, size)">
                                                            <span class="text-[8px] font-bold uppercase text-amber-600 block -mt-0.5">Baixo</span>
                                                        </template>
                                                    </div>
                                                </td>
                                            </template>

                                            <td class="py-3 px-4 font-black text-right text-xs text-slate-900 dark:text-white">
                                                <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 font-mono" x-text="getColorTotalStock(cIndex)"></span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- CARD 4: Galeria de Fotos do Produto -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-100 dark:border-slate-800 space-y-6">
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                            <div>
                                <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center">
                                        <span class="material-symbols-outlined">photo_library</span>
                                    </div>
                                    Fotos do Produto
                                </h2>
                                <p class="text-xs text-slate-400 mt-1">A primeira foto é a capa principal do anúncio. Arraste para reordenar.</p>
                            </div>
                            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-900/30 px-3 py-1 rounded-full">
                                <span x-text="images.length"></span> fotos
                            </span>
                        </div>

                        {{-- Dica de fotografia universal --}}
                        <div class="p-3.5 bg-blue-50 dark:bg-blue-900/20 rounded-2xl border border-blue-100 dark:border-blue-800/40 text-xs text-blue-800 dark:text-blue-300 flex items-start gap-2.5">
                            <span class="material-symbols-outlined text-base shrink-0 mt-0.5 text-blue-600">lightbulb</span>
                            <p><strong>Dica de imagem:</strong> Utilize fotos com boa iluminação e fundo limpo. A 1ª foto é a capa principal exibida nos cards da loja.</p>
                        </div>

                        {{-- Upload Box --}}
                        <div class="border-2 border-dashed border-slate-200 dark:border-slate-700 hover:border-primary rounded-2xl p-6 text-center cursor-pointer transition-colors relative"
                            @dragover.prevent="$el.classList.add('border-primary', 'bg-primary/5')"
                            @dragleave.prevent="$el.classList.remove('border-primary', 'bg-primary/5')"
                            @drop.prevent="$el.classList.remove('border-primary', 'bg-primary/5'); handleFileUpload($event)">
                            <input type="file" multiple accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                @change="handleFileUpload($event)">
                            <span class="material-symbols-outlined text-4xl text-slate-400 mb-2 block">add_photo_alternate</span>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-300">Clique ou arraste as fotos do produto aqui</p>
                            <p class="text-xs text-slate-400 mt-1">Suporta JPG, PNG, WEBP em alta definição</p>
                        </div>

                        {{-- Ou adicionar URL externa --}}
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                            <input type="url" x-model="externalUrl" placeholder="Ou cole a URL de uma imagem da internet (https://...)"
                                class="w-full sm:flex-1 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs font-mono focus:ring-primary focus:border-primary border">
                            <button type="button" @click="addExternalImage()"
                                class="w-full sm:w-auto px-4 py-2.5 bg-slate-900 dark:bg-slate-700 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition-colors">
                                Adicionar
                            </button>
                        </div>

                        {{-- Preview Grid Reordenável --}}
                        <div x-ref="sortableContainer" class="grid grid-cols-2 sm:grid-cols-4 gap-4" x-show="images.length > 0">
                            <template x-for="(img, index) in images" :key="img.id">
                                <div class="relative aspect-square rounded-2xl overflow-hidden border-2 border-slate-200 dark:border-slate-700 group cursor-move shadow-sm bg-slate-100 dark:bg-slate-800">
                                    <img :src="img.preview" class="w-full h-full object-cover">
                                    
                                    {{-- Badge Capa --}}
                                    <div x-show="index === 0" class="absolute top-2 left-2 px-2 py-0.5 bg-primary text-white text-[9px] font-black uppercase rounded-md shadow-md">
                                        Foto Capa
                                    </div>
                                    <div x-show="index > 0" class="absolute top-2 left-2 px-1.5 py-0.5 bg-black/60 text-white text-[9px] font-bold rounded">
                                        #<span x-text="index + 1"></span>
                                    </div>

                                    <button type="button" @click="removeImage(index)"
                                        class="absolute top-2 right-2 size-7 bg-red-500 text-white rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-md">
                                        <span class="material-symbols-outlined text-sm">delete</span>
                                    </button>

                                    <input type="hidden" :name="`image_order[${img.id}]`" :value="index">
                                    <template x-if="img.type === 'url'">
                                        <input type="hidden" name="image_urls[]" :value="img.preview">
                                    </template>
                                </div>
                            </template>
                        </div>

                        {{-- Hidden file inputs container --}}
                        <div x-ref="fileInputs" class="hidden"></div>
                    </div>

                    <!-- CARD 5: Ficha Técnica & Especificações -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-100 dark:border-slate-800 space-y-6">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-3 border-b border-slate-100 dark:border-slate-800 pb-4">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 flex items-center justify-center">
                                <span class="material-symbols-outlined">tune</span>
                            </div>
                            Ficha Técnica & Especificações
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-2">
                                    Material Principal / Composição
                                </label>
                                <input type="text" name="material" value="{{ old('material') }}"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-primary focus:border-primary transition-all font-semibold py-3 px-4 border-2 text-sm"
                                    placeholder="Ex: RipStop, Aço/Polímero, Alumínio CNC, Poliéster 190T...">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-2">
                                    Especificação Secundária / Mecanismo / Solado
                                </label>
                                <input type="text" name="sole_material" value="{{ old('sole_material') }}"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-primary focus:border-primary transition-all font-semibold py-3 px-4 border-2 text-sm"
                                    placeholder="Ex: Gearbox V2, Drag 8kg, Coluna d'água 3000mm, EVA...">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-2">
                                Outras Especificações (FPS, Dimensões, Calibre, etc.)
                            </label>
                            <textarea name="technical_specifications" rows="3"
                                class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-primary focus:border-primary transition-all font-medium p-4 border-2 text-sm"
                                placeholder="Velocidade/FPS: 380 FPS&#10;Capacidade / Lotação: 4 pessoas&#10;Garantia: 90 dias&#10;Origem: Importado / Nacional">{{ old('technical_specifications') }}</textarea>
                        </div>
                    </div>
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-2">
                                    Material do Cabedal
                                </label>
                                <input type="text" name="material" value="{{ old('material') }}"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-primary focus:border-primary transition-all font-semibold py-3 px-4 border-2 text-sm"
                                    placeholder="Ex: Mesh respirável / Couro Sintético">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-2">
                                    Solado & Amortecimento
                                </label>
                                <input type="text" name="sole_material" value="{{ old('sole_material') }}"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-primary focus:border-primary transition-all font-semibold py-3 px-4 border-2 text-sm"
                                    placeholder="Ex: Borracha antiderrapante com EVA">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-2">
                                Outras Especificações (Pisada, Drop, Origem)
                            </label>
                            <textarea name="technical_specifications" rows="3"
                                class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-primary focus:border-primary transition-all font-medium p-4 border-2 text-sm"
                                placeholder="Indicação: Corrida e Treino&#10;Tipo de Pisada: Neutra&#10;Garantia do Fabricante: Contra defeito de fabricação">{{ old('technical_specifications') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Coluna Lateral (1/3) -->
                <div class="space-y-8">

                    <!-- CARD Valores & Promoção -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-100 dark:border-slate-800 space-y-6">
                        <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                            <span class="material-symbols-outlined text-emerald-600">payments</span>
                            Preço & Estoque
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
                                    Preço de Venda (R$) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-slate-400">R$</span>
                                    <input type="number" step="0.01" name="price" x-model.number="price" required
                                        class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-emerald-500 focus:border-emerald-500 transition-all font-black py-3.5 pl-12 pr-4 border-2 text-lg text-slate-900 dark:text-white"
                                        placeholder="0,00">
                                </div>
                                @error('price') <p class="mt-1 text-xs text-red-500 font-bold">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5">
                                    Preço "De" Riscado (Opcional)
                                </label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-slate-400">R$</span>
                                    <input type="number" step="0.01" name="compare_price" x-model.number="comparePrice"
                                        class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-emerald-500 focus:border-emerald-500 transition-all font-bold py-3 pl-12 pr-4 border-2 text-sm text-slate-900 dark:text-white"
                                        placeholder="Ex: 599,90">
                                </div>
                                <template x-if="comparePrice > price && price > 0">
                                    <p class="mt-1.5 text-xs text-emerald-600 font-bold flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm">local_offer</span>
                                        Desconto de <span x-text="Math.round(((comparePrice - price) / comparePrice) * 100)"></span>% OFF
                                    </p>
                                </template>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-1.5 flex items-center justify-between">
                                    <span>Estoque Total (Unidades) <span class="text-red-500">*</span></span>
                                    <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-0.5">
                                        <span class="material-symbols-outlined text-[11px]">sync</span> matriz ativa
                                    </span>
                                </label>
                                <input type="number" name="stock" :value="totalMatrixStock" readonly required min="0"
                                    class="w-full bg-slate-100 dark:bg-slate-800/80 border-slate-200 dark:border-slate-700 rounded-2xl font-black py-3 px-4 border-2 text-base text-slate-900 dark:text-white cursor-not-allowed">
                                @error('stock') <p class="mt-1 text-xs text-red-500 font-bold">{{ $message }}</p> @enderror
                            </div>

                            <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-primary focus:ring-primary size-5">
                                    <div>
                                        <span class="text-xs font-bold text-slate-900 dark:text-white block">Produto Ativo na Loja</span>
                                        <span class="text-[10px] text-slate-400">Visível no catálogo e buscas</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- CARD Categorias / Departamentos -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-100 dark:border-slate-800 space-y-4">
                        <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                            <span class="material-symbols-outlined text-blue-600">category</span>
                            Categorias / Departamentos <span class="text-red-500">*</span>
                        </h2>

                        <div class="space-y-2 max-h-56 overflow-y-auto custom-scrollbar pr-1">
                            @foreach($categories as $category)
                                <label class="flex items-center gap-3 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer transition-colors">
                                    <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                        {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}
                                        class="rounded border-slate-300 text-primary focus:ring-primary size-4">
                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ $category->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('categories') <p class="text-xs text-red-500 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <!-- CARD Embalagem / Frete (Melhor Envio) -->
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-slate-100 dark:border-slate-800 space-y-4">
                        <div class="border-b border-slate-100 dark:border-slate-800 pb-3">
                            <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <span class="material-symbols-outlined text-amber-600">local_shipping</span>
                                Dimensões do Pacote / Frete
                            </h2>
                            <p class="text-[11px] text-slate-400 mt-1">Medidas reais para cálculo de frete no Melhor Envio:</p>
                        </div>

                        {{-- Predefinições Rápidas de Frete --}}
                        <div class="flex flex-wrap gap-1.5">
                            <button type="button" @click="setPackageDimensions(0.3, 5, 20, 25)"
                                class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-[10px] font-bold text-slate-600 dark:text-slate-300 transition-colors">
                                👕 Roupa (300g)
                            </button>
                            <button type="button" @click="setPackageDimensions(0.8, 12, 22, 34)"
                                class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-[10px] font-bold text-slate-600 dark:text-slate-300 transition-colors">
                                👟 Calçado (800g)
                            </button>
                            <button type="button" @click="setPackageDimensions(1.5, 15, 25, 35)"
                                class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-[10px] font-bold text-slate-600 dark:text-slate-300 transition-colors">
                                📦 Médio (1.5kg)
                            </button>
                            <button type="button" @click="setPackageDimensions(4.0, 20, 25, 75)"
                                class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-[10px] font-bold text-slate-600 dark:text-slate-300 transition-colors">
                                ⛺ Grande/Barraca (4kg)
                            </button>
                        </div>

                        <div class="grid grid-cols-2 gap-3 pt-1">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 mb-1">Peso (kg)</label>
                                <input type="number" step="0.001" name="weight" x-model="boxWeight"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl py-2 px-3 text-xs font-bold border">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 mb-1">Altura (cm)</label>
                                <input type="number" step="0.1" name="height" x-model="boxHeight"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl py-2 px-3 text-xs font-bold border">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 mb-1">Largura (cm)</label>
                                <input type="number" step="0.1" name="width" x-model="boxWidth"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl py-2 px-3 text-xs font-bold border">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 mb-1">Comprimento (cm)</label>
                                <input type="number" step="0.1" name="length" x-model="boxLength"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl py-2 px-3 text-xs font-bold border">
                            </div>
                        </div>
                    </div>

                    <!-- Botões de Envio -->
                    <div class="pt-2 flex flex-col gap-3">
                        <button type="submit"
                            class="w-full py-4 bg-primary hover:opacity-95 text-white font-black text-base rounded-2xl transition-all shadow-xl shadow-primary/25 flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined">save</span>
                            Salvar Produto
                        </button>
                        <a href="{{ route('admin.products.index') }}"
                            class="w-full py-3.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-600 dark:text-slate-300 font-bold rounded-2xl transition-all text-center text-xs">
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
                    brand: '{{ old('brand') }}',
                    gender: '{{ old('gender', 'Unissex') }}',
                    price: {{ old('price', 0) }},
                    comparePrice: {{ old('compare_price', 0) }},
                    
                    // Universal variation presets & active sizes
                    allSizes: ['PP', 'P', 'M', 'G', 'GG', 'XG', 'XXG', 'Único', '36', '37', '38', '39', '40', '41', '42', '43', '44', '0.20g', '0.25g', '0.28g', '6mm', 'Manivela Direita', 'Manivela Esquerda'],
                    selectedSizes: {!! json_encode(old('sizes', ['Único'])) !!},
                    customSizeInput: '',

                    // Box dimensions for Melhor Envio
                    boxWeight: {{ old('weight', 0.5) }},
                    boxHeight: {{ old('height', 10) }},
                    boxWidth: {{ old('width', 20) }},
                    boxLength: {{ old('length', 25) }},

                    setPackageDimensions(w, h, wi, l) {
                        this.boxWeight = w;
                        this.boxHeight = h;
                        this.boxWidth = wi;
                        this.boxLength = l;
                    },

                    resetShoeBoxDimensions() {
                        this.setPackageDimensions(0.8, 12, 22, 34);
                    },

                    // Colors
                    colors: [
                        { hex: '#000000', name: 'Preto' }
                    ],

                    // Variation Inventory Matrix
                    matrixStock: {!! json_encode(old('matrix', [0 => ['Único' => 5]])) !!},

                    // Images
                    images: [],
                    externalUrl: '',
                    fileCount: 0,

                    toggleSize(size) {
                        const index = this.selectedSizes.indexOf(size);
                        if (index > -1) {
                            const sizeStock = this.getSizeTotalStock(size);
                            if (sizeStock > 0) {
                                if (!confirm(`A opção "${size}" possui ${sizeStock} unidades cadastradas em estoque. Deseja realmente desativar esta variação?`)) {
                                    return;
                                }
                            }
                            this.selectedSizes.splice(index, 1);
                        } else {
                            this.selectedSizes.push(size);
                            this.sortSizes();
                        }
                    },

                    setSizes(sizesArray) {
                        this.selectedSizes = [...sizesArray];
                    },

                    selectAllSizes() {
                        this.selectedSizes = [...this.allSizes];
                    },

                    sortSizes() {
                        this.selectedSizes.sort((a, b) => {
                            const numA = parseFloat(a);
                            const numB = parseFloat(b);
                            if (!isNaN(numA) && !isNaN(numB)) return numA - numB;
                            return a.localeCompare(b);
                        });
                    },

                    addCustomSize() {
                        const val = this.customSizeInput.trim();
                        if (val && !this.selectedSizes.includes(val)) {
                            this.selectedSizes.push(val);
                            if (!this.allSizes.includes(val)) {
                                this.allSizes.push(val);
                                this.allSizes.sort((a, b) => {
                                    const numA = parseFloat(a);
                                    const numB = parseFloat(b);
                                    if (!isNaN(numA) && !isNaN(numB)) return numA - numB;
                                    return a.localeCompare(b);
                                });
                            }
                            this.sortSizes();
                            this.customSizeInput = '';
                        }
                    },

                    addColor() {
                        this.colors.push({ hex: '#000000', name: '' });
                    },

                    addPresetColor(hex, name) {
                        this.colors.push({ hex: hex, name: name });
                    },

                    removeColor(index) {
                        const colorStock = this.getColorTotalStock(index);
                        const colorName = this.colors[index]?.name || this.colors[index]?.hex || `Cor ${index + 1}`;
                        if (colorStock > 0) {
                            if (!confirm(`A cor "${colorName}" possui ${colorStock} unidades cadastradas em estoque na matriz. Deseja realmente remover esta cor?`)) {
                                return;
                            }
                        }
                        this.colors.splice(index, 1);
                        delete this.matrixStock[index];
                    },

                    // Matrix Methods
                    getMatrixStock(cIndex, size) {
                        if (!this.matrixStock[cIndex]) return 0;
                        const val = this.matrixStock[cIndex][size];
                        return (typeof val !== 'undefined' && val !== null) ? val : 0;
                    },

                    setMatrixStock(cIndex, size, val) {
                        if (!this.matrixStock[cIndex]) {
                            this.matrixStock[cIndex] = {};
                        }
                        this.matrixStock[cIndex][size] = Math.max(0, parseInt(val) || 0);
                    },

                    getColorTotalStock(cIndex) {
                        let sum = 0;
                        for (const size of this.selectedSizes) {
                            sum += this.getMatrixStock(cIndex, size);
                        }
                        return sum;
                    },

                    getSizeTotalStock(size) {
                        let sum = 0;
                        for (let i = 0; i < this.colors.length; i++) {
                            sum += this.getMatrixStock(i, size);
                        }
                        return sum;
                    },

                    get totalMatrixStock() {
                        let total = 0;
                        for (let i = 0; i < this.colors.length; i++) {
                            for (const size of this.selectedSizes) {
                                total += this.getMatrixStock(i, size);
                            }
                        }
                        return total;
                    },

                    isOutOfStock(cIndex, size) {
                        return this.getMatrixStock(cIndex, size) === 0;
                    },

                    hasLowStock(cIndex, size) {
                        const s = this.getMatrixStock(cIndex, size);
                        return s > 0 && s <= 3;
                    },

                    get totalOutOfStock() {
                        let count = 0;
                        for (let i = 0; i < this.colors.length; i++) {
                            for (const size of this.selectedSizes) {
                                if (this.isOutOfStock(i, size)) count++;
                            }
                        }
                        return count;
                    },

                    get totalLowStock() {
                        let count = 0;
                        for (let i = 0; i < this.colors.length; i++) {
                            for (const size of this.selectedSizes) {
                                if (this.hasLowStock(i, size)) count++;
                            }
                        }
                        return count;
                    },

                    bulkFillMatrixPrompt() {
                        const val = prompt('Informe a quantidade padrão para todas as variações:', '5');
                        if (val !== null && !isNaN(parseInt(val))) {
                            const qty = Math.max(0, parseInt(val));
                            for (let i = 0; i < this.colors.length; i++) {
                                for (const size of this.selectedSizes) {
                                    this.setMatrixStock(i, size, qty);
                                }
                            }
                        }
                    },

                    clearAllMatrixStock() {
                        if (confirm('Deseja realmente zerar o estoque de todas as combinações da matriz?')) {
                            for (let i = 0; i < this.colors.length; i++) {
                                for (const size of this.selectedSizes) {
                                    this.setMatrixStock(i, size, 0);
                                }
                            }
                        }
                    },

                    init() {
                        this.$nextTick(() => {
                            if (this.$refs.sortableContainer) {
                                new Sortable(this.$refs.sortableContainer, {
                                    animation: 150,
                                    onEnd: (evt) => {
                                        const movedItem = this.images.splice(evt.oldIndex, 1)[0];
                                        this.images.splice(evt.newIndex, 0, movedItem);
                                    }
                                });
                            }
                        });
                    },

                    handleFileUpload(event) {
                        const files = Array.from(event.target.files || (event.dataTransfer && event.dataTransfer.files) || []);
                        files.forEach(file => {
                            const reader = new FileReader();
                            const id = 'file_' + (++this.fileCount);

                            reader.onload = (e) => {
                                this.images.push({
                                    id: id,
                                    preview: e.target.result,
                                    type: 'file'
                                });
                                this.addFileInput(id, file);
                            };
                            reader.readAsDataURL(file);
                        });
                        if (event.target) event.target.value = '';
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

                    addExternalImage() {
                        if (this.externalUrl) {
                            this.images.push({
                                id: 'ext_' + Date.now(),
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

                    validateAndSubmit(event) {
                        const form = (event && event.target && event.target.tagName === 'FORM')
                            ? event.target
                            : (this.$refs.sneakerFormRef || this.$el.querySelector('form') || document.getElementById('sneakerFormTag'));

                        if (!form) return;

                        const formData = new FormData(form);

                        let errors = [];

                        if (!formData.get('name')) errors.push("O nome do produto é obrigatório.");
                        if (!formData.get('price')) errors.push("O preço de venda é obrigatório.");
                        if (formData.getAll('categories[]').length === 0) errors.push("Selecione pelo menos uma categoria ou marca.");
                        if (this.selectedSizes.length === 0) errors.push("Selecione pelo menos uma variação ou tamanho para o produto.");
                        if (this.images.length === 0) errors.push("Adicione pelo menos uma foto para o produto.");

                        if (errors.length > 0) {
                            alert("Por favor, preencha os campos obrigatórios:\n\n- " + errors.join("\n- "));
                            return;
                        }

                        form.submit();
                    }
                };
            }

            // Manter compatibilidade com ambos os nomes
            window.sneakerForm = productForm;
        </script>
    @endpush
</x-admin-layout>
