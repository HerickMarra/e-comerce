<x-admin-layout>
    @section('page_title', 'Configurações do Sistema')

    <div class="max-w-4xl space-y-8" x-data="{
        activeTab: 'branding',
        primaryColor: '{{ $settings['primary_color'] }}',
        logoPreview: '{{ $settings['store_logo'] ? asset('storage/' . $settings['store_logo']) : '' }}',
        logoSize: '{{ $settings['store_logo_size'] }}',
        storeIcon: '{{ $settings['store_icon'] }}',
        previewName: '{{ $settings['store_name'] }}',
        showWebhookToken: false
    }">
        @if(session('success'))
            <div class="p-4 bg-primary/10 border border-primary/20 text-primary rounded-xl flex items-center gap-3">
                <span class="material-symbols-outlined">check_circle</span>
                <span class="text-sm font-bold">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Tab Navigation --}}
        <div
            class="bg-white rounded-2xl border border-slate-100 shadow-sm p-1.5 grid grid-cols-2 lg:grid-cols-5 gap-1 md:gap-2">
            <button type="button" @click="activeTab = 'branding'"
                class="w-full flex flex-col md:flex-row items-center justify-center gap-2 px-3 py-3 rounded-xl text-xs md:text-sm font-bold transition-all text-center"
                :class="activeTab === 'branding' ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50'">
                <span class="material-symbols-outlined text-[1.2rem] md:text-lg">palette</span>
                <span>Identidade Visual</span>
            </button>
            <button type="button" @click="activeTab = 'payment'"
                class="w-full flex flex-col md:flex-row items-center justify-center gap-2 px-3 py-3 rounded-xl text-xs md:text-sm font-bold transition-all text-center"
                :class="activeTab === 'payment' ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50'">
                <span class="material-symbols-outlined text-[1.2rem] md:text-lg">payments</span>
                <span>Gateway de Pagamento</span>
            </button>
            <button type="button" @click="activeTab = 'shipping'"
                class="w-full flex flex-col md:flex-row items-center justify-center gap-2 px-3 py-3 rounded-xl text-xs md:text-sm font-bold transition-all text-center"
                :class="activeTab === 'shipping' ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50'">
                <span class="material-symbols-outlined text-[1.2rem] md:text-lg">local_shipping</span>
                <span>Frete (EnviaMais)</span>
            </button>
            <button type="button" @click="activeTab = 'store'"
                class="w-full flex flex-col md:flex-row items-center justify-center gap-2 px-3 py-3 rounded-xl text-xs md:text-sm font-bold transition-all text-center"
                :class="activeTab === 'store' ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50'">
                <span class="material-symbols-outlined text-[1.2rem] md:text-lg">storefront</span>
                <span>Loja Física</span>
            </button>
            <button type="button" @click="activeTab = 'email'"
                class="w-full col-span-2 lg:col-span-1 flex flex-col md:flex-row items-center justify-center gap-2 px-3 py-3 rounded-xl text-xs md:text-sm font-bold transition-all text-center"
                :class="activeTab === 'email' ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50'">
                <span class="material-symbols-outlined text-[1.2rem] md:text-lg">mail</span>
                <span>E-mail (SMTP)</span>
            </button>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data"
            class="space-y-8">
            @csrf
            @method('PUT')

            {{-- ============================= --}}
            {{-- TAB: IDENTIDADE VISUAL --}}
            {{-- ============================= --}}
            <div x-show="activeTab === 'branding'" x-transition class="space-y-6">

                {{-- Live Preview --}}
                <div
                    class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-8 text-white overflow-hidden relative">
                    <div class="absolute top-0 right-0 size-64 rounded-full opacity-5"
                        :style="'background: ' + primaryColor" style="transform: translate(30%, -30%)"></div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-6">Pré-visualização</p>
                    <div class="flex items-center gap-4">
                        <div class="size-14 rounded-2xl overflow-hidden border-2 border-white/10 flex-shrink-0 bg-slate-700 flex items-center justify-center"
                            :style="logoPreview ? '' : 'background: ' + primaryColor">
                            <img x-show="logoPreview" :src="logoPreview" class="object-contain p-1"
                                :style="'height: ' + logoSize + '%'">
                            <span x-show="!logoPreview"
                                class="material-symbols-outlined text-white text-2xl">chair</span>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold tracking-tight" x-text="previewName || 'Nome da Loja'"></h3>
                            <p class="text-sm text-slate-400 mt-0.5">{{ $settings['store_tagline'] }}</p>
                        </div>
                    </div>
                    <div class="mt-8 flex gap-3">
                        <div class="h-2 w-16 rounded-full" :style="'background: ' + primaryColor"></div>
                        <div class="h-2 w-8 rounded-full bg-white/10"></div>
                        <div class="h-2 w-12 rounded-full bg-white/10"></div>
                    </div>
                </div>

                {{-- Store Identity --}}
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-50 flex items-center gap-4 bg-slate-50/50">
                        <div class="size-10 bg-indigo-500 rounded-xl flex items-center justify-center text-white">
                            <span class="material-symbols-outlined text-base">storefront</span>
                        </div>
                        <div>
                            <h2 class="font-bold text-slate-900">Informações da Loja</h2>
                            <p class="text-xs text-slate-500">Nome, slogan e descrição exibidos publicamente.</p>
                        </div>
                    </div>
                    <div class="p-8 space-y-6">
                        <div>
                            <label
                                class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2 block">Nome
                                da Loja</label>
                            <input type="text" name="store_name" value="{{ $settings['store_name'] }}"
                                x-model="previewName"
                                class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-slate-900 focus:border-slate-900 transition-all font-bold"
                                placeholder="Ex: VerveHome">
                        </div>
                        <div>
                            <label
                                class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2 block">Tagline
                                / Slogan</label>
                            <input type="text" name="store_tagline" value="{{ $settings['store_tagline'] }}"
                                class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-slate-900 focus:border-slate-900 transition-all"
                                placeholder="Ex: Móveis de alto padrão para sua casa">
                        </div>
                    </div>
                </div>

                {{-- Logo Upload --}}
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-50 flex items-center gap-4 bg-slate-50/50">
                        <div class="size-10 bg-amber-500 rounded-xl flex items-center justify-center text-white">
                            <span class="material-symbols-outlined text-base">image</span>
                        </div>
                        <div>
                            <h2 class="font-bold text-slate-900">Logo da Loja</h2>
                            <p class="text-xs text-slate-500">Formatos: PNG, JPG, SVG. Recomendado: 256x256px.</p>
                        </div>
                    </div>
                    <div class="p-8">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                            {{-- Logo Upload --}}
                            <div class="space-y-4">
                                <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 block">Logo
                                    em Imagem</label>
                                <div class="flex items-start gap-6">
                                    <div
                                        class="size-24 rounded-2xl bg-slate-100 border-2 border-dashed border-slate-200 overflow-hidden flex items-center justify-center flex-shrink-0 relative group">
                                        <img x-show="logoPreview" :src="logoPreview"
                                            class="w-full h-full object-contain p-2">
                                        <span x-show="!logoPreview"
                                            class="material-symbols-outlined text-3xl text-slate-300">image</span>

                                        <template x-if="logoPreview">
                                            <button type="button"
                                                @click="logoPreview = ''; $refs.logoInput.value = ''; $refs.removeLogo.value = '1'"
                                                class="absolute inset-0 bg-red-500/80 text-white opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                                <span class="material-symbols-outlined">delete</span>
                                            </button>
                                        </template>
                                    </div>

                                    <div class="flex-1 space-y-4">
                                        <label
                                            class="flex flex-col items-center justify-center w-full p-4 border-2 border-dashed border-slate-200 rounded-2xl cursor-pointer hover:border-sidebar-dark hover:bg-slate-50 transition-all">
                                            <span
                                                class="material-symbols-outlined text-2xl text-slate-400 mb-1">cloud_upload</span>
                                            <p class="text-[10px] font-bold text-slate-500 text-center leading-tight">
                                                Clique para alterar a logo</p>
                                            <input type="file" name="store_logo" x-ref="logoInput" class="hidden"
                                                accept="image/*"
                                                @change="let f = $event.target.files[0]; if(f) { let r = new FileReader(); r.onload = e => { logoPreview = e.target.result; $refs.removeLogo.value = '0'; }; r.readAsDataURL(f); }">
                                        </label>

                                        <div class="space-y-2" x-show="logoPreview">
                                            <div class="flex items-center justify-between">
                                                <label class="text-[9px] font-bold text-slate-400 uppercase">Tamanho da
                                                    Logo: <span x-text="logoSize + '%'"></span></label>
                                                <button type="button" @click="logoSize = '100'"
                                                    class="text-[9px] text-primary font-bold">Resetar</button>
                                            </div>
                                            <input type="range" name="store_logo_size" x-model="logoSize" min="10"
                                                max="200" step="5"
                                                class="w-full h-1.5 bg-slate-100 rounded-lg appearance-none cursor-pointer accent-primary">
                                        </div>

                                        <input type="hidden" name="remove_logo" x-ref="removeLogo" value="0">
                                        <p class="text-[9px] text-slate-400 leading-relaxed">Se enviar uma logo, o nome
                                            e o ícone não aparecerão no cabeçalho.</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Icon Selection --}}
                            <div class="space-y-4">
                                <label
                                    class="text-[10px] font-bold uppercase tracking-widest text-slate-400 block">Ícone
                                    da Marca (Caso não use logo)</label>
                                <div class="flex items-center gap-6">
                                    <div class="size-24 rounded-2xl flex items-center justify-center text-white shadow-lg transition-all"
                                        :style="'background: ' + primaryColor">
                                        <span class="material-symbols-outlined text-4xl" x-text="storeIcon"></span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="grid grid-cols-4 gap-2">
                                            @foreach(['chair', 'home', 'shopping_bag', 'eco', 'diamond', 'bolt', 'auto_awesome', 'weekend'] as $icon)
                                                <button type="button" @click="storeIcon = '{{ $icon }}'"
                                                    class="size-10 rounded-xl border-2 flex items-center justify-center transition-all"
                                                    :class="storeIcon === '{{ $icon }}' ? 'border-sidebar-dark bg-slate-50 scale-110 shadow-sm' : 'border-slate-100 hover:border-slate-300'">
                                                    <span
                                                        class="material-symbols-outlined text-lg text-slate-600">{{ $icon }}</span>
                                                </button>
                                            @endforeach
                                        </div>
                                        <input type="hidden" name="store_icon" :value="storeIcon">
                                        <p class="text-[9px] text-slate-400 mt-3 italic">Este ícone aparecerá ao lado do
                                            nome da loja se não houver logo em imagem.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Color Palette --}}
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-50 flex items-center gap-4 bg-slate-50/50">
                        <div class="size-10 rounded-xl flex items-center justify-center text-white"
                            :style="'background: ' + primaryColor">
                            <span class="material-symbols-outlined text-base">palette</span>
                        </div>
                        <div>
                            <h2 class="font-bold text-slate-900">Paleta de Cores</h2>
                            <p class="text-xs text-slate-500">Cor principal usada em botões, links e destaques.</p>
                        </div>
                    </div>
                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label
                                    class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-4 block">Cor
                                    Principal</label>
                                <div class="flex gap-3 flex-wrap mb-5">
                                    @foreach(['#10b981', '#3b82f6', '#8b5cf6', '#f59e0b', '#ef4444', '#ec4899', '#06b6d4', '#84cc16'] as $color)
                                        <button type="button" @click="primaryColor = '{{ $color }}'"
                                            class="size-10 rounded-xl transition-all hover:scale-110 ring-2 ring-offset-2"
                                            :class="primaryColor === '{{ $color }}' ? 'ring-slate-900 scale-110' : 'ring-transparent'"
                                            style="background: {{ $color }}"></button>
                                    @endforeach
                                </div>
                                <div class="flex items-center gap-3">
                                    <input type="color" name="primary_color" x-model="primaryColor"
                                        class="size-12 rounded-xl cursor-pointer border-2 border-slate-200 p-0.5">
                                    <input type="text" x-model="primaryColor" readonly
                                        class="flex-1 bg-slate-50 border-slate-200 rounded-xl px-4 py-3 text-sm font-mono font-bold">
                                </div>
                            </div>

                            <div>
                                <label
                                    class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-4 block">Cor
                                    Secundária (Fundo escuro)</label>
                                <div class="flex gap-3 flex-wrap mb-5">
                                    @foreach(['#0f172a', '#1e293b', '#111827', '#1c1917', '#0c0a09', '#030712', '#1a1a2e', '#0a0a0a'] as $color)
                                        <button type="button" x-data="{ sec: '{{ $settings['secondary_color'] }}' }"
                                            @click="$root.querySelector('[name=secondary_color]').value = '{{ $color }}'"
                                            class="size-10 rounded-xl transition-all hover:scale-110 border border-slate-200"
                                            style="background: {{ $color }}"></button>
                                    @endforeach
                                </div>
                                <div class="flex items-center gap-3">
                                    <input type="color" name="secondary_color"
                                        value="{{ $settings['secondary_color'] }}"
                                        class="size-12 rounded-xl cursor-pointer border-2 border-slate-200 p-0.5">
                                    <input type="text" value="{{ $settings['secondary_color'] }}" readonly
                                        class="flex-1 bg-slate-50 border-slate-200 rounded-xl px-4 py-3 text-sm font-mono font-bold">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================= --}}
            {{-- TAB: GATEWAY DE PAGAMENTO --}}
            {{-- ============================= --}}
            <div x-show="activeTab === 'payment'" x-transition class="space-y-6">
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="p-8 border-b border-slate-50 flex items-center gap-4 bg-slate-50/50">
                        <div class="size-12 bg-slate-900 rounded-2xl flex items-center justify-center text-white">
                            <span class="material-symbols-outlined">payments</span>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Configurações do Asaas</h2>
                            <p class="text-xs text-slate-500">Gerencie suas chaves de API para processamento de
                                pagamentos.</p>
                        </div>
                    </div>

                    <div class="p-8 space-y-8">
                        <div>
                            <label
                                class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-3 block">Ambiente
                                (URL da API)</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <label
                                    class="flex items-center gap-4 p-4 border-2 rounded-2xl cursor-pointer transition-all {{ str_contains($settings['asaas_url'] ?? '', 'sandbox') ? 'border-amber-400 bg-amber-50/50' : 'border-slate-200 hover:border-slate-300' }}"
                                    onclick="document.querySelector('[name=asaas_url]').value = 'https://sandbox.asaas.com/api/v3'">
                                    <div class="size-10 bg-amber-100 rounded-xl flex items-center justify-center">
                                        <span class="material-symbols-outlined text-amber-600">science</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-900">Sandbox</p>
                                        <p class="text-[10px] text-slate-500">Testes sem cobrança real</p>
                                    </div>
                                </label>
                                <label
                                    class="flex items-center gap-4 p-4 border-2 rounded-2xl cursor-pointer transition-all {{ !str_contains($settings['asaas_url'] ?? '', 'sandbox') ? 'border-primary bg-primary/5' : 'border-slate-200 hover:border-slate-300' }}"
                                    onclick="document.querySelector('[name=asaas_url]').value = 'https://api.asaas.com/v3/'">
                                    <div class="size-10 bg-primary/10 rounded-xl flex items-center justify-center">
                                        <span class="material-symbols-outlined text-primary">verified</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-900">Produção</p>
                                        <p class="text-[10px] text-slate-500">Cobranças reais</p>
                                    </div>
                                </label>
                            </div>
                            <input type="text" name="asaas_url" value="{{ $settings['asaas_url'] }}"
                                class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 text-sm font-mono focus:ring-slate-900 focus:border-slate-900 transition-all"
                                placeholder="https://sandbox.asaas.com/api/v3">
                        </div>

                        <div x-data="{ show: false }">
                            <label
                                class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-3 block">Chave
                                de API (Access Token)</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="asaas_api_key"
                                    value="{{ $settings['asaas_api_key'] ? '********************************' : '' }}"
                                    class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 pr-14 text-sm focus:ring-slate-900 focus:border-slate-900 transition-all font-mono"
                                    placeholder="$aact_prod_...">
                                <button type="button" @click="show = !show"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-900 transition-colors">
                                    <span class="material-symbols-outlined text-lg"
                                        x-text="show ? 'visibility_off' : 'visibility'"></span>
                                </button>
                            </div>
                            @if($settings['asaas_api_key'])
                                <div class="mt-3 p-3 bg-blue-50 border border-blue-100 rounded-xl flex items-start gap-2">
                                    <span class="material-symbols-outlined text-blue-500 text-sm mt-0.5">info</span>
                                    <p class="text-[10px] text-blue-600 leading-relaxed">
                                        Uma chave está configurada e armazenada com segurança. Para mantê-la, deixe o campo
                                        como está.
                                    </p>
                                </div>
                            @else
                                <div class="mt-3 p-3 bg-amber-50 border border-amber-100 rounded-xl flex items-start gap-2">
                                    <span class="material-symbols-outlined text-amber-500 text-sm mt-0.5">warning</span>
                                    <p class="text-[10px] text-amber-700 leading-relaxed">
                                        Nenhuma chave configurada. Os pagamentos não funcionarão até que uma chave válida
                                        seja inserida.
                                    </p>
                                </div>
                            @endif
                        </div>

                        {{-- Webhook Section --}}
                        <div class="pt-6 border-t border-slate-100">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-3 block">URL
                                do Webhook</label>
                            <p class="text-xs text-slate-500 mb-3">Copie e cadastre no painel Asaas em
                                <strong>Integrações → Webhook</strong>.
                            </p>
                            <div class="flex items-center gap-2">
                                <input type="text" readonly id="webhook-url-field" value="{{ url('/webhooks/asaas') }}"
                                    class="flex-1 bg-slate-50 border-slate-200 rounded-xl px-4 py-3 text-sm font-mono text-slate-700">
                                <button type="button"
                                    onclick="navigator.clipboard.writeText(document.getElementById('webhook-url-field').value); this.innerHTML='<span class=\'material-symbols-outlined text-emerald-500\'>check</span>'; setTimeout(()=>this.innerHTML='<span class=\'material-symbols-outlined\'>content_copy</span>',2000);"
                                    class="p-3 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors text-slate-600">
                                    <span class="material-symbols-outlined">content_copy</span>
                                </button>
                            </div>
                            <div class="mt-6" x-data="{ showTok: false }">
                                <label
                                    class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-3 block">Token
                                    de Validação (opcional)</label>
                                <p class="text-[10px] text-slate-400 mb-3">Se definido, valida o header <code
                                        class="bg-slate-100 px-1 rounded">asaas-access-token</code> em cada requisição.
                                </p>
                                <div class="relative">
                                    <input :type="showTok ? 'text' : 'password'" name="asaas_webhook_token"
                                        value="{{ $settings['asaas_webhook_token'] ?? '' ? '********************************' : '' }}"
                                        class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 pr-14 text-sm font-mono focus:ring-slate-900 focus:border-slate-900 transition-all"
                                        placeholder="Deixe em branco para não validar">
                                    <button type="button" @click="showTok = !showTok"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-900 transition-colors">
                                        <span class="material-symbols-outlined text-lg"
                                            x-text="showTok ? 'visibility_off' : 'visibility'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'shipping'" x-transition class="space-y-6">
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden" x-data="{ 
                        enviamaisMode: '{{ $settings['enviamais_dev_mode'] ?? 'production' }}',
                        testLoading: false,
                        testMessage: '',
                        testSuccess: false,
                        get enviamaisUrl() {
                            return this.enviamaisMode === 'sandbox' ? 'https://hmg.enviamais.com.br/api/partner/v1' : 'https://api.enviamais.com.br/api/partner/v1';
                        },
                        testConnection() {
                            this.testLoading = true;
                            this.testMessage = '';
                            fetch('{{ route('admin.settings.test-enviamais') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                this.testLoading = false;
                                this.testMessage = data.message;
                                this.testSuccess = data.success;
                            })
                            .catch(error => {
                                this.testLoading = false;
                                this.testMessage = 'Erro ao conectar. Verifique o console.';
                                this.testSuccess = false;
                            });
                        }
                     }">
                    <div
                        class="p-8 border-b border-slate-50 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/50">
                        <div class="flex items-center gap-4">
                            <div
                                class="size-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                                <span class="material-symbols-outlined">local_shipping</span>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">Configurações do EnviaMais</h2>
                                <p class="text-xs text-slate-500 font-medium">Configure seu token para calcular fretes
                                    automaticamente.</p>
                            </div>
                        </div>
                        <button type="button" @click="testConnection()" :disabled="testLoading"
                            class="px-6 py-3 bg-white border border-slate-200 rounded-2xl text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all flex items-center gap-2 shadow-sm disabled:opacity-50">
                            <span class="material-symbols-outlined text-base" :class="testLoading ? 'animate-spin' : ''"
                                x-text="testLoading ? 'sync' : 'api'"></span>
                            Testar Conexão
                        </button>
                    </div>

                    <div class="p-8 space-y-8">
                        <div x-show="testMessage" x-cloak x-transition
                            :class="testSuccess ? 'bg-emerald-50 border-emerald-100 text-emerald-700' : 'bg-red-50 border-red-100 text-red-700'"
                            class="p-4 rounded-2xl border text-sm font-bold flex items-center gap-3">
                            <span class="material-symbols-outlined"
                                x-text="testSuccess ? 'check_circle' : 'error'"></span>
                            <span x-text="testMessage"></span>
                        </div>

                        <div>
                            <label
                                class="text-[10px] font-bold uppercase tracking-[0.15em] text-slate-400 mb-4 block italic">Ambiente
                                de Conexão</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <label
                                    class="group relative flex items-center gap-4 p-5 border-2 rounded-2xl cursor-pointer transition-all overflow-hidden"
                                    :class="enviamaisMode === 'sandbox' ? 'border-amber-400 bg-amber-50/30' : 'border-slate-100 hover:border-slate-200 bg-slate-50/50'">
                                    <input type="radio" name="enviamais_dev_mode" value="sandbox" class="hidden"
                                        x-model="enviamaisMode">
                                    <div class="size-12 rounded-xl flex items-center justify-center transition-colors"
                                        :class="enviamaisMode === 'sandbox' ? 'bg-amber-100 text-amber-600' : 'bg-slate-200 text-slate-400 group-hover:bg-slate-300'">
                                        <span class="material-symbols-outlined">science</span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-bold text-slate-900">Homologação</p>
                                        <p class="text-[10px] text-slate-500 font-medium">Ambiente de testes (HMG)</p>
                                    </div>
                                    <div x-show="enviamaisMode === 'sandbox'"
                                        class="absolute -right-2 -top-2 size-8 bg-amber-400 rounded-full flex items-center justify-center text-white scale-75">
                                        <span class="material-symbols-outlined text-sm">done</span>
                                    </div>
                                </label>

                                <label
                                    class="group relative flex items-center gap-4 p-5 border-2 rounded-2xl cursor-pointer transition-all overflow-hidden"
                                    :class="enviamaisMode === 'production' ? 'border-primary bg-primary/5' : 'border-slate-100 hover:border-slate-200 bg-slate-50/50'">
                                    <input type="radio" name="enviamais_dev_mode" value="production" class="hidden"
                                        x-model="enviamaisMode">
                                    <div class="size-12 rounded-xl flex items-center justify-center transition-colors"
                                        :class="enviamaisMode === 'production' ? 'bg-primary/20 text-primary' : 'bg-slate-200 text-slate-400 group-hover:bg-slate-300'">
                                        <span class="material-symbols-outlined text-primary">verified</span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-bold text-slate-900">Produção</p>
                                        <p class="text-[10px] text-slate-500 font-medium">Pedidos e envios reais</p>
                                    </div>
                                    <div x-show="enviamaisMode === 'production'"
                                        class="absolute -right-2 -top-2 size-8 bg-primary rounded-full flex items-center justify-center text-white scale-75">
                                        <span class="material-symbols-outlined text-sm">done</span>
                                    </div>
                                </label>
                            </div>

                            <div class="mt-4 p-4 bg-slate-900 rounded-2xl border border-slate-800 shadow-inner">
                                <label
                                    class="text-[9px] font-black uppercase tracking-widest text-slate-500 mb-2 block">Requisições
                                    enviadas para:</label>
                                <code class="text-xs font-mono text-emerald-400 break-all" x-text="enviamaisUrl"></code>
                            </div>
                        </div>

                        <div x-data="{ show: false }">
                            <label
                                class="text-[10px] font-bold uppercase tracking-[0.15em] text-slate-400 mb-3 block italic">Token
                                de Acesso (Api-Key)</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="enviamais_api_key"
                                    value="{{ $settings['enviamais_api_key'] ? '********************************' : '' }}"
                                    class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-4 pr-14 text-sm focus:ring-slate-900 focus:border-slate-900 transition-all font-mono shadow-inner"
                                    placeholder="Seu token EnviaMais...">
                                <button type="button" @click="show = !show"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-900 transition-colors">
                                    <span class="material-symbols-outlined text-lg"
                                        x-text="show ? 'visibility_off' : 'visibility'"></span>
                                </button>
                            </div>
                            @if($settings['enviamais_api_key'])
                                <div
                                    class="mt-3 p-3 bg-blue-50/50 border border-blue-100 rounded-xl flex items-start gap-3">
                                    <span class="material-symbols-outlined text-blue-500 text-sm mt-0.5">info</span>
                                    <p class="text-[10px] text-blue-600 font-medium leading-relaxed">O token está
                                        configurado e encriptado no banco de dados. Para alterá-lo, apague este campo e cole
                                        o novo valor.</p>
                                </div>
                            @endif
                        </div>

                        <div class="p-5 bg-indigo-50/30 rounded-2xl border border-indigo-100 flex gap-4">
                            <div
                                class="size-10 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600 flex-shrink-0">
                                <span class="material-symbols-outlined">help</span>
                            </div>
                            <div class="space-y-1">
                                <p class="text-xs font-bold text-indigo-900">Como funciona o cálculo?</p>
                                <p class="text-[10px] text-indigo-500 font-medium leading-relaxed">
                                    O sistema utiliza o peso e as dimensões (altura, largura, comprimento) de cada
                                    produto para consultar a API do EnviaMais em tempo real. Certifique-se de que todos
                                    os seus produtos tenham essas informações preenchidas para evitar erros no carrinho.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================= --}}
            {{-- TAB: LOJA FÍSICA --}}
            {{-- ============================= --}}
            <div x-show="activeTab === 'store'" x-transition class="space-y-6" x-data="{
                loadingCep: false,
                address: {
                    zip: '{{ $settings['store_zip'] ?? '' }}',
                    street: '{{ $settings['store_street'] ?? '' }}',
                    neighborhood: '{{ $settings['store_neighborhood'] ?? '' }}',
                    city: '{{ $settings['store_city'] ?? '' }}',
                    state: '{{ $settings['store_state'] ?? '' }}'
                },
                searchCep() {
                    let zip = this.address.zip.replace(/\D/g, '');
                    if (zip.length !== 8) return;

                    this.loadingCep = true;
                    fetch(`https://viacep.com.br/ws/${zip}/json/`)
                        .then(res => res.json())
                        .then(data => {
                            if (!data.erro) {
                                this.address.street = data.logradouro;
                                this.address.neighborhood = data.bairro;
                                this.address.city = data.localidade;
                                this.address.state = data.uf;
                                // Focus number field after autofill
                                this.$nextTick(() => {
                                    this.$refs.storeNumber.focus();
                                });
                            }
                        })
                        .finally(() => this.loadingCep = false);
                }
            }">
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="p-8 border-b border-slate-50 flex items-center gap-4 bg-slate-50/50">
                        <div class="size-12 bg-slate-900 rounded-2xl flex items-center justify-center text-white">
                            <span class="material-symbols-outlined">storefront</span>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Configurações da Loja Física</h2>
                            <p class="text-xs text-slate-500">Endereço para retirada e controle de disponibilidade.</p>
                        </div>
                    </div>

                    <div class="p-8 space-y-8">
                        {{-- Pickup Toggle --}}
                        <div
                            class="flex items-center justify-between p-6 bg-slate-50 rounded-2xl border border-slate-100">
                            <div class="flex items-center gap-4">
                                <div
                                    class="size-10 bg-white rounded-xl flex items-center justify-center text-slate-400 border border-slate-200 shadow-sm">
                                    <span class="material-symbols-outlined">hail</span>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900">Ativar Retirada na Loja</p>
                                    <p class="text-[10px] text-slate-500">Permite que clientes escolham retirar o pedido
                                        no seu endereço.</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="enable_store_pickup" value="1" class="sr-only peer" {{ ($settings['enable_store_pickup'] ?? '1') === '1' ? 'checked' : '' }}>
                                <div
                                    class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                                </div>
                            </label>
                        </div>

                        {{-- Address Fields --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-1">
                                <label
                                    class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2 block">CEP</label>
                                <div class="relative group">
                                    <input type="text" name="store_zip" x-model="address.zip" x-mask="99999-999"
                                        @blur="searchCep"
                                        class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-slate-900 focus:border-slate-900 transition-all font-bold"
                                        placeholder="00000-000">
                                    <div class="absolute right-4 top-1/2 -translate-y-1/2" x-show="loadingCep">
                                        <span
                                            class="material-symbols-outlined animate-spin text-sm text-primary">sync</span>
                                    </div>
                                </div>
                            </div>
                            <div class="hidden md:block"></div>

                            <div class="md:col-span-2">
                                <label
                                    class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2 block">Rua
                                    / Logradouro</label>
                                <input type="text" name="store_street" x-model="address.street"
                                    class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-slate-900 focus:border-slate-900 transition-all font-bold"
                                    placeholder="Ex: Avenida Paulista">
                            </div>
                            <div>
                                <label
                                    class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2 block">Número</label>
                                <input type="text" name="store_number" x-ref="storeNumber"
                                    value="{{ $settings['store_number'] ?? '' }}"
                                    class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-slate-900 focus:border-slate-900 transition-all font-bold"
                                    placeholder="Ex: 1000">
                            </div>
                            <div>
                                <label
                                    class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2 block">Bairro</label>
                                <input type="text" name="store_neighborhood" x-model="address.neighborhood"
                                    class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-slate-900 focus:border-slate-900 transition-all font-bold"
                                    placeholder="Ex: Cerqueira César">
                            </div>
                            <div>
                                <label
                                    class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2 block">Cidade</label>
                                <input type="text" name="store_city" x-model="address.city"
                                    class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-slate-900 focus:border-slate-900 transition-all font-bold"
                                    placeholder="Ex: São Paulo">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2 block">Estado</label>
                                    <input type="text" name="store_state" x-model="address.state"
                                        class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-slate-900 focus:border-slate-900 transition-all font-bold"
                                        placeholder="Ex: SP">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================= --}}
            {{-- TAB: EMAIL E SMTP --}}
            {{-- ============================= --}}
            <div x-show="activeTab === 'email'" x-transition class="space-y-6">
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="p-8 border-b border-slate-50 flex items-center gap-4 bg-slate-50/50">
                        <div class="size-12 bg-slate-900 rounded-2xl flex items-center justify-center text-white">
                            <span class="material-symbols-outlined">mail</span>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Configurações de E-mail (SMTP)</h2>
                            <p class="text-xs text-slate-500">Configure o servidor para envio de e-mails transacionais
                                (pedidos, recuperação de senha).</p>
                        </div>
                    </div>

                    <div class="p-8 space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label
                                    class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2 block">Mailer</label>
                                <select name="mail_mailer"
                                    class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-slate-900 focus:border-slate-900 transition-all font-bold">
                                    <option value="smtp" {{ ($settings['mail_mailer'] ?? 'smtp') === 'smtp' ? 'selected' : '' }}>SMTP</option>
                                    <option value="mailgun" {{ ($settings['mail_mailer'] ?? '') === 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                    <option value="postmark" {{ ($settings['mail_mailer'] ?? '') === 'postmark' ? 'selected' : '' }}>Postmark</option>
                                    <option value="ses" {{ ($settings['mail_mailer'] ?? '') === 'ses' ? 'selected' : '' }}>Amazon SES</option>
                                    <option value="log" {{ ($settings['mail_mailer'] ?? '') === 'log' ? 'selected' : '' }}>Log (Dev)</option>
                                </select>
                            </div>

                            <div>
                                <label
                                    class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2 block">Criptografia
                                    (Encryption)</label>
                                <select name="mail_encryption"
                                    class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-slate-900 focus:border-slate-900 transition-all font-bold">
                                    <option value="tls" {{ ($settings['mail_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ ($settings['mail_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="" {{ empty($settings['mail_encryption']) ? 'selected' : '' }}>Nenhuma
                                    </option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label
                                    class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2 block">Servidor
                                    SMTP (Host)</label>
                                <input type="text" name="mail_host" value="{{ $settings['mail_host'] ?? '' }}"
                                    class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-slate-900 focus:border-slate-900 transition-all font-mono"
                                    placeholder="Ex: smtp.mailtrap.io ou smtp.gmail.com">
                            </div>

                            <div>
                                <label
                                    class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2 block">Porta
                                    (Port)</label>
                                <input type="text" name="mail_port" value="{{ $settings['mail_port'] ?? '587' }}"
                                    class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-slate-900 focus:border-slate-900 transition-all font-mono"
                                    placeholder="Ex: 587 ou 465">
                            </div>

                            <div>
                                <label
                                    class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2 block">Usuário
                                    de Autenticação (Username)</label>
                                <input type="text" name="mail_username" value="{{ $settings['mail_username'] ?? '' }}"
                                    class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-slate-900 focus:border-slate-900 transition-all font-mono"
                                    placeholder="Seu usuário SMTP">
                            </div>

                            <div x-data="{ show: false }" class="md:col-span-2">
                                <label
                                    class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2 block">Senha
                                    (Password)</label>
                                <div class="relative">
                                    <input :type="show ? 'text' : 'password'" name="mail_password"
                                        value="{{ !empty($settings['mail_password']) ? '********************************' : '' }}"
                                        class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 pr-14 text-sm focus:ring-slate-900 focus:border-slate-900 transition-all font-mono"
                                        placeholder="Sua senha SMTP">
                                    <button type="button" @click="show = !show"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-900 transition-colors">
                                        <span class="material-symbols-outlined text-lg"
                                            x-text="show ? 'visibility_off' : 'visibility'"></span>
                                    </button>
                                </div>
                                @if(!empty($settings['mail_password']))
                                    <p class="text-[10px] text-blue-500 mt-2"><span class="font-bold">Nota:</span> Uma senha
                                        já está configurada. Para mantê-la, deixe como está.</p>
                                @endif
                            </div>

                            <div class="col-span-1 border-t border-slate-100 pt-6 mt-2 md:col-span-2"></div>

                            <div>
                                <label
                                    class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2 block">E-mail
                                    do Remetente (From Address)</label>
                                <input type="email" name="mail_from_address"
                                    value="{{ $settings['mail_from_address'] ?? '' }}"
                                    class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-slate-900 focus:border-slate-900 transition-all font-bold"
                                    placeholder="Ex: contato@minhaloja.com.br">
                            </div>

                            <div>
                                <label
                                    class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2 block">Nome
                                    do Remetente (From Name)</label>
                                <input type="text" name="mail_from_name" value="{{ $settings['mail_from_name'] ?? '' }}"
                                    class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-slate-900 focus:border-slate-900 transition-all font-bold"
                                    placeholder="Ex: Loja Santo Lar">
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- Save Button --}}
            <div class="flex justify-end">
                <button type="submit"
                    class="bg-primary text-white px-10 py-4 rounded-2xl font-bold text-sm transition-all shadow-xl shadow-primary/20 flex items-center gap-2 group">
                    <span class="material-symbols-outlined text-lg">save</span>
                    Salvar Configurações
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>