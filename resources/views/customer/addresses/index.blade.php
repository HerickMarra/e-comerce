<x-customer-layout title="Meus Endereços" active="addresses">
    <div x-data="addressManager" class="space-y-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Meus Endereços</h1>
                <p class="text-slate-500 mt-1 font-medium">Gerencie seus endereços de entrega e cobrança.</p>
            </div>
            <button @click="openCreateModal()"
                class="flex items-center justify-center gap-2 bg-primary hover:bg-emerald-600 text-white font-bold py-3 px-6 rounded-2xl transition-all shadow-lg shadow-primary/20 whitespace-nowrap">
                <span class="material-symbols-outlined">add</span>
                Adicionar Novo Endereço
            </button>
        </div>

        @if (session('status'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center gap-3 font-bold text-sm shadow-sm transition-all">
                <span class="material-symbols-outlined">check_circle</span>
                {{ session('status') === 'address-created' ? 'Endereço cadastrado com sucesso!' : '' }}
                {{ session('status') === 'address-updated' ? 'Endereço atualizado com sucesso!' : '' }}
                {{ session('status') === 'address-deleted' ? 'Endereço excluído com sucesso!' : '' }}
                {{ session('status') === 'address-default-set' ? 'Endereço principal definido!' : '' }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach ($addresses as $address)
                <div
                    class="bg-white dark:bg-slate-900 border-2 {{ $address->is_default ? 'border-primary' : 'border-slate-100 dark:border-slate-800' }} rounded-3xl p-6 shadow-sm relative group transition-all hover:shadow-md">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="p-2 {{ $address->is_default ? 'bg-primary/10 text-primary' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }} rounded-xl">
                            <span
                                class="material-symbols-outlined">{{ $address->label === 'Trabalho' ? 'work' : 'home' }}</span>
                        </div>
                        @if ($address->is_default)
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 uppercase tracking-wide">
                                Principal
                            </span>
                        @else
                            <form action="{{ route('customer.addresses.set-default', $address) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="text-xs font-bold text-slate-400 hover:text-primary transition-colors">
                                    Definir como principal
                                </button>
                            </form>
                        @endif
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white mb-2 text-lg">
                        {{ $address->recipient_name }}
                        @if($address->label) <span class="text-slate-400 text-sm font-medium">({{ $address->label }})</span>
                        @endif
                    </h3>
                    <div class="space-y-1 text-sm text-slate-600 dark:text-slate-400 font-medium">
                        <p>{{ $address->street }}, {{ $address->number }}
                            {{ $address->complement ? '- ' . $address->complement : '' }}</p>
                        <p>Bairro {{ $address->neighborhood }}</p>
                        <p>{{ $address->city }}, {{ $address->state }}</p>
                        <p class="font-black pt-1">CEP: {{ $address->zip_code }}</p>
                    </div>
                    <div class="mt-6 flex items-center gap-4 border-t border-slate-100 dark:border-slate-800 pt-4">
                        <button @click="openEditModal({{ json_encode($address) }})"
                            class="text-sm font-bold text-slate-900 dark:text-white hover:text-primary flex items-center gap-1 transition-colors">
                            <span class="material-symbols-outlined text-lg">edit</span>
                            Editar
                        </button>
                        <form action="{{ route('customer.addresses.destroy', $address) }}" method="POST"
                            onsubmit="return confirm('Tem certeza que deseja excluir este endereço?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-sm font-bold text-red-500 hover:text-red-600 flex items-center gap-1 transition-colors">
                                <span class="material-symbols-outlined text-lg">delete</span>
                                Excluir
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach

            <button @click="openCreateModal()"
                class="border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-3xl p-6 flex flex-col items-center justify-center gap-3 text-slate-400 hover:border-primary hover:text-primary hover:bg-slate-50 dark:hover:bg-slate-900/50 transition-all min-h-[200px]">
                <span class="material-symbols-outlined text-4xl">add_circle</span>
                <span class="font-black">Adicionar outro endereço</span>
            </button>
        </div>

        <!-- Modal Endereço -->
        <template x-teleport="body">
            <div x-show="modalOpen"
                class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

                <div @click.away="modalOpen = false"
                    class="bg-white dark:bg-slate-900 w-full max-w-2xl rounded-[2.5rem] p-8 md:p-10 shadow-2xl relative max-h-[90vh] overflow-y-auto"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0">

                    <button @click="modalOpen = false"
                        class="absolute top-8 right-8 text-slate-400 hover:text-slate-600 transition-colors">
                        <span class="material-symbols-outlined text-3xl">close</span>
                    </button>

                    <div class="mb-8">
                        <h2 class="text-2xl font-black text-slate-900 dark:text-white"
                            x-text="editMode ? 'Editar Endereço' : 'Novo Endereço'"></h2>
                        <p class="text-slate-500 font-medium">Preencha os dados abaixo para entrega.</p>
                    </div>

                    <form :action="formAction" method="POST" class="space-y-6">
                        @csrf
                        <template x-if="editMode">
                            <input type="hidden" name="_method" value="PATCH">
                        </template>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="col-span-full space-y-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">Identificação
                                    (Ex: Casa, Trabalho)</label>
                                <input type="text" name="label" x-model="formData.label"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all"
                                    placeholder="Casa, Trabalho, etc." required>
                            </div>

                            <div class="col-span-full space-y-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">Nome do
                                    Recebedor</label>
                                <input type="text" name="recipient_name" x-model="formData.recipient_name"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all"
                                    required>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">CEP</label>
                                <div class="relative">
                                    <input type="text" name="zip_code" x-model="formData.zip_code" x-mask="99999-999"
                                        @blur="searchCep"
                                        class="w-full bg-slate-50 dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all"
                                        placeholder="00000-000" required>
                                    <template x-if="loadingCep">
                                        <div class="absolute right-4 top-1/2 -translate-y-1/2">
                                            <div
                                                class="w-5 h-5 border-2 border-primary border-t-transparent rounded-full animate-spin">
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">Estado
                                    (UF)</label>
                                <input type="text" name="state" x-model="formData.state" x-mask="aa"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all uppercase"
                                    placeholder="SP" required>
                            </div>

                            <div class="col-span-full md:col-span-1 space-y-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">Cidade</label>
                                <input type="text" name="city" x-model="formData.city"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all"
                                    required>
                            </div>

                            <div class="col-span-full md:col-span-1 space-y-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">Bairro</label>
                                <input type="text" name="neighborhood" x-model="formData.neighborhood"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all"
                                    required>
                            </div>

                            <div class="col-span-full md:col-span-1 space-y-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">Logradouro
                                    (Rua/Av)</label>
                                <input type="text" name="street" x-model="formData.street"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all"
                                    required>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">Número</label>
                                <input type="text" name="number" x-model="formData.number"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all"
                                    required>
                            </div>

                            <div class="col-span-full space-y-2">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">Complemento
                                    (Opcional)</label>
                                <input type="text" name="complement" x-model="formData.complement"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all"
                                    placeholder="Ex: Apto 123, Bloco A">
                            </div>

                            <div class="col-span-full flex items-center gap-3 py-2">
                                <input type="checkbox" name="is_default" value="1" x-model="formData.is_default"
                                    id="is_default"
                                    class="w-6 h-6 rounded-lg border-2 border-slate-200 text-primary focus:ring-primary/30 transition-all">
                                <label for="is_default"
                                    class="text-sm font-bold text-slate-700 dark:text-slate-300 cursor-pointer">Definir
                                    como endereço principal</label>
                            </div>
                        </div>

                        <div class="flex gap-4 mt-8 pt-6 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" @click="modalOpen = false"
                                class="flex-1 py-4 px-6 rounded-2xl font-bold bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all">Cancelar</button>
                            <button type="submit"
                                class="flex-1 py-4 px-6 rounded-2xl font-black bg-primary text-white hover:bg-emerald-600 shadow-xl shadow-primary/20 transition-all">Salvar
                                Endereço</button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('addressManager', () => ({
                modalOpen: false,
                editMode: false,
                loadingCep: false,
                formAction: '{{ route('customer.addresses.store') }}',
                formData: {
                    label: '',
                    recipient_name: '{{ Auth::user()->name }}',
                    zip_code: '',
                    street: '',
                    number: '',
                    complement: '',
                    neighborhood: '',
                    city: '',
                    state: '',
                    is_default: false
                },

                openCreateModal() {
                    this.editMode = false;
                    this.formAction = '{{ route('customer.addresses.store') }}';
                    this.formData = {
                        label: '',
                        recipient_name: '{{ Auth::user()->name }}',
                        zip_code: '',
                        street: '',
                        number: '',
                        complement: '',
                        neighborhood: '',
                        city: '',
                        state: '',
                        is_default: false
                    };
                    this.modalOpen = true;
                },

                openEditModal(address) {
                    this.editMode = true;
                    this.formAction = `/minha-conta/enderecos/${address.id}`;
                    this.formData = { ...address };
                    this.modalOpen = true;
                },

                searchCep() {
                    const cep = this.formData.zip_code.replace(/\D/g, '');
                    if (cep.length === 8) {
                        this.loadingCep = true;
                        fetch(`https://viacep.com.br/ws/${cep}/json/`)
                            .then(response => response.json())
                            .then(data => {
                                if (!data.erro) {
                                    this.formData.street = data.logradouro;
                                    this.formData.neighborhood = data.bairro;
                                    this.formData.city = data.localidade;
                                    this.formData.state = data.uf;
                                }
                            })
                            .finally(() => this.loadingCep = false);
                    }
                }
            }));
        });
    </script>
</x-customer-layout>