<x-admin-layout>
    <div class="space-y-8" x-data="{
        showSummaryModal: false,
        summaryLoading: false,
        activeSummaryTab: 'orders',
        summaryData: null,
        copiedField: '',
        copyText(text, field) {
            if (!text) return;
            navigator.clipboard.writeText(text);
            this.copiedField = field;
            setTimeout(() => { this.copiedField = ''; }, 2000);
        },
        openWhatsApp(customText = '') {
            if (!this.summaryData?.user?.clean_phone) {
                alert('Este usuário não possui telefone cadastrado.');
                return;
            }
            const name = this.summaryData.user.name || 'Cliente';
            const defaultMsg = `Olá ${name}, tudo bem? Aqui é da equipe Denner Tênis! Como podemos te ajudar?`;
            const msg = customText || defaultMsg;
            const phone = this.summaryData.user.clean_phone.startsWith('55') 
                ? this.summaryData.user.clean_phone 
                : '55' + this.summaryData.user.clean_phone;
            window.open(`https://wa.me/${phone}?text=${encodeURIComponent(msg)}`, '_blank');
        },
        openSummary(userId) {
            this.showSummaryModal = true;
            this.summaryLoading = true;
            this.summaryData = null;
            this.activeSummaryTab = 'orders';
            this.copiedField = '';

            fetch(`/admin/users/${userId}/summary`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.summaryData = data;
                    } else {
                        alert('Erro ao carregar dados do usuário.');
                        this.showSummaryModal = false;
                    }
                })
                .catch(() => {
                    alert('Erro de conexão ao buscar o resumo do usuário.');
                    this.showSummaryModal = false;
                })
                .finally(() => {
                    this.summaryLoading = false;
                });
        }
    }" @keydown.escape.window="showSummaryModal = false">

        {{-- Cabeçalho Principal --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-3 py-1 bg-primary/10 text-primary font-black text-xs uppercase tracking-wider rounded-full">
                        Módulo de Usuários
                    </span>
                    <span class="text-xs text-slate-400 font-bold">• {{ $stats['total_users'] }} cadastrados</span>
                </div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Gestão de Usuários e Clientes</h1>
                <p class="text-slate-500 text-sm mt-1">Monitore compras, histórico de pedidos, permissões e perfis de clientes da loja.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.users.create') }}"
                    class="inline-flex items-center justify-center px-6 py-3.5 bg-primary text-white font-bold rounded-2xl transition-all shadow-lg shadow-primary/25 hover:opacity-95 hover:scale-[1.02] active:scale-95 gap-2 text-sm">
                    <span class="material-symbols-outlined text-xl">person_add</span>
                    Novo Usuário
                </a>
            </div>
        </div>

        {{-- Alertas de Sucesso / Erro --}}
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center gap-3 shadow-sm">
                <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                <p class="text-sm font-bold">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl flex items-center gap-3 shadow-sm">
                <span class="material-symbols-outlined text-red-600">error</span>
                <p class="text-sm font-bold">{{ session('error') }}</p>
            </div>
        @endif

        {{-- Cards de Estatísticas e Métricas Gerais --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            {{-- Card 1: Total de Usuários --}}
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="size-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center shadow-inner">
                        <span class="material-symbols-outlined text-2xl">group</span>
                    </div>
                    @if($stats['new_this_month'] > 0)
                        <span class="text-[10px] font-black uppercase tracking-wider px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full border border-emerald-200">
                            +{{ $stats['new_this_month'] }} este mês
                        </span>
                    @endif
                </div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total de Cadastros</p>
                <h3 class="text-3xl font-black text-slate-900 mt-1">{{ number_format($stats['total_users'], 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-500 mt-1 font-medium">Contas registradas no sistema</p>
            </div>

            {{-- Card 2: Clientes Compradores --}}
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="size-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shadow-inner">
                        <span class="material-symbols-outlined text-2xl">shopping_bag</span>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-wider px-2.5 py-1 bg-blue-50 text-blue-700 rounded-full border border-blue-200">
                        Consumidores
                    </span>
                </div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Clientes da Loja</p>
                <h3 class="text-3xl font-black text-slate-900 mt-1">{{ number_format($stats['total_customers'], 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-500 mt-1 font-medium">Usuários com perfil de cliente</p>
            </div>

            {{-- Card 3: Volume Comprado / LTV --}}
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="size-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-inner">
                        <span class="material-symbols-outlined text-2xl">payments</span>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-wider px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full border border-emerald-200">
                        Total Pago
                    </span>
                </div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Volume Total em Vendas</p>
                <h3 class="text-3xl font-black text-slate-900 mt-1">R$ {{ number_format($stats['total_spent'], 2, ',', '.') }}</h3>
                <p class="text-xs text-slate-500 mt-1 font-medium">Faturamento gerado por clientes</p>
            </div>

            {{-- Card 4: Administradores & Equipe --}}
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
                <div class="flex items-center justify-between mb-4">
                    <div class="size-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center shadow-inner">
                        <span class="material-symbols-outlined text-2xl">shield_person</span>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-wider px-2.5 py-1 bg-purple-50 text-purple-700 rounded-full border border-purple-200">
                        Gestão
                    </span>
                </div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Equipe Administrativa</p>
                <h3 class="text-3xl font-black text-slate-900 mt-1">{{ number_format($stats['total_admins'], 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-500 mt-1 font-medium">Super Admins e Gestores</p>
            </div>
        </div>

        {{-- Filtros, Busca e Ações em Linha --}}
        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm space-y-4">
            <form action="{{ route('admin.users.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
                {{-- Campo de Busca --}}
                <div class="md:col-span-5 relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Buscar por nome, e-mail, CPF ou telefone..."
                        class="w-full bg-slate-50 border-slate-200 rounded-2xl pl-12 pr-4 py-3 text-sm focus:ring-slate-900 focus:border-slate-900 transition-all font-medium">
                </div>

                {{-- Filtro por Função --}}
                <div class="md:col-span-3">
                    <select name="role"
                        class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3 text-sm focus:ring-slate-900 focus:border-slate-900 transition-all font-bold text-slate-700">
                        <option value="">Todas as Funções</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Ordenação --}}
                <div class="md:col-span-3">
                    <select name="sort"
                        class="w-full bg-slate-50 border-slate-200 rounded-2xl px-4 py-3 text-sm focus:ring-slate-900 focus:border-slate-900 transition-all font-bold text-slate-700">
                        <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Mais Recentes</option>
                        <option value="most_orders" {{ request('sort') === 'most_orders' ? 'selected' : '' }}>Mais Pedidos Realizados</option>
                        <option value="highest_spent" {{ request('sort') === 'highest_spent' ? 'selected' : '' }}>Maior Valor Gasto (R$)</option>
                        <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Nome (A - Z)</option>
                        <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Nome (Z - A)</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Mais Antigos</option>
                    </select>
                </div>

                {{-- Botões Filtrar e Limpar --}}
                <div class="md:col-span-1 flex items-center gap-2">
                    <button type="submit"
                        class="w-full bg-slate-900 text-white p-3 rounded-2xl flex items-center justify-center hover:bg-slate-800 transition-colors shadow-sm"
                        title="Filtrar">
                        <span class="material-symbols-outlined text-lg">tune</span>
                    </button>
                    @if(request()->hasAny(['search', 'role', 'sort']))
                        <a href="{{ route('admin.users.index') }}"
                            class="p-3 bg-slate-100 text-slate-600 rounded-2xl hover:bg-slate-200 transition-colors flex items-center justify-center"
                            title="Limpar filtros">
                            <span class="material-symbols-outlined text-lg">close</span>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tabela de Usuários Completa --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-100 uppercase text-[10px] font-black text-slate-400 tracking-widest">
                            <th class="px-6 py-4">Usuário / Perfil</th>
                            <th class="px-6 py-4">Documento & Contato</th>
                            <th class="px-6 py-4">Função</th>
                            <th class="px-6 py-4">Pedidos & Volume</th>
                            <th class="px-6 py-4">Cadastro / Acesso</th>
                            <th class="px-6 py-4 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($users as $user)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                {{-- Usuário / Perfil --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3.5">
                                        <div class="size-11 rounded-2xl bg-gradient-to-br from-primary/20 to-primary/5 text-primary border border-primary/20 flex items-center justify-center font-black text-base shadow-sm shrink-0">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-slate-900 group-hover:text-primary transition-colors">
                                                    {{ $user->name }}
                                                </span>
                                                @if(auth()->id() === $user->id)
                                                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 text-[10px] font-black uppercase tracking-wider border border-emerald-200">
                                                        Você
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-xs text-slate-400 font-medium">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- CPF / Telefone --}}
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        <p class="text-xs font-mono font-bold text-slate-700">
                                            {{ $user->cpf ?: '---' }}
                                        </p>
                                        <div class="flex items-center gap-1.5 text-xs text-slate-500 font-medium">
                                            <span>{{ $user->phone ?: 'Sem telefone' }}</span>
                                            @if($user->phone)
                                                @php
                                                    $cleanPhone = preg_replace('/\D/', '', $user->phone);
                                                    if ($cleanPhone && !str_starts_with($cleanPhone, '55')) {
                                                        $cleanPhone = '55' . $cleanPhone;
                                                    }
                                                @endphp
                                                <a href="https://wa.me/{{ $cleanPhone }}" target="_blank"
                                                    class="text-emerald-600 hover:text-emerald-700 p-0.5 rounded hover:bg-emerald-50 transition-colors"
                                                    title="Conversar no WhatsApp">
                                                    <span class="material-symbols-outlined text-sm">chat</span>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Função / Roles --}}
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1.5">
                                        @forelse($user->roles as $role)
                                            @php
                                                $roleClasses = match(strtolower($role->name)) {
                                                    'super admin' => 'bg-purple-100 text-purple-800 border-purple-200',
                                                    'admin' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                                    'manager' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                    default => 'bg-slate-100 text-slate-700 border-slate-200',
                                                };
                                            @endphp
                                            <span class="px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider border {{ $roleClasses }}">
                                                {{ $role->name }}
                                            </span>
                                        @empty
                                            <span class="px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-500 border border-slate-200">
                                                Cliente
                                            </span>
                                        @endforelse
                                    </div>
                                </td>

                                {{-- Pedidos & Volume --}}
                                <td class="px-6 py-4">
                                    <div class="space-y-0.5">
                                        <div class="flex items-center gap-1.5">
                                            <span class="material-symbols-outlined text-sm text-slate-400">shopping_cart</span>
                                            <span class="font-bold text-slate-800">
                                                {{ $user->orders_count }} {{ $user->orders_count === 1 ? 'pedido' : 'pedidos' }}
                                            </span>
                                        </div>
                                        <p class="text-xs font-black text-emerald-600 font-mono">
                                            R$ {{ number_format((float) ($user->total_spent ?? 0), 2, ',', '.') }}
                                        </p>
                                    </div>
                                </td>

                                {{-- Cadastro / Acesso --}}
                                <td class="px-6 py-4">
                                    <div class="space-y-0.5">
                                        <div class="flex items-center gap-1 text-xs font-bold text-slate-700">
                                            <span class="material-symbols-outlined text-xs text-slate-400">calendar_today</span>
                                            <span>{{ $user->created_at->format('d/m/Y') }}</span>
                                        </div>
                                        <p class="text-[11px] text-slate-400">
                                            @if($user->last_login_at)
                                                Login {{ $user->last_login_at->diffForHumans() }}
                                            @else
                                                Sem login registrado
                                            @endif
                                        </p>
                                    </div>
                                </td>

                                {{-- Ações --}}
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        {{-- Botão: Resumo Geral (Abre Modal) --}}
                                        <button type="button" @click="openSummary({{ $user->id }})"
                                            class="inline-flex items-center gap-1 px-3 py-2 rounded-xl bg-primary/10 text-primary hover:bg-primary hover:text-white transition-all text-xs font-black shadow-sm group/btn"
                                            title="Ver Resumo Geral do Usuário">
                                            <span class="material-symbols-outlined text-base group-hover/btn:scale-110 transition-transform">visibility</span>
                                            <span class="hidden sm:inline">Resumo</span>
                                        </button>

                                        {{-- Botão Editar --}}
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                            class="size-9 rounded-xl bg-slate-50 text-slate-500 hover:bg-slate-100 hover:text-slate-900 transition-colors flex items-center justify-center border border-slate-200 shadow-sm"
                                            title="Editar Usuário">
                                            <span class="material-symbols-outlined text-base">edit</span>
                                        </a>

                                        {{-- Botão Excluir --}}
                                        @if(auth()->id() !== $user->id)
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                                onsubmit="return confirm('Tem certeza que deseja excluir o usuário {{ $user->name }}? Esta ação não pode ser desfeita.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="size-9 rounded-xl bg-slate-50 text-slate-400 hover:bg-red-50 hover:text-red-600 transition-colors flex items-center justify-center border border-slate-200 shadow-sm"
                                                    title="Excluir Usuário">
                                                    <span class="material-symbols-outlined text-base">delete</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="size-16 rounded-full bg-slate-50 flex items-center justify-center text-slate-300">
                                            <span class="material-symbols-outlined text-3xl">group_off</span>
                                        </div>
                                        <h4 class="text-base font-bold text-slate-800">Nenhum usuário encontrado</h4>
                                        <p class="text-xs text-slate-400 max-w-sm">Tente redefinir os filtros de busca ou cadastre um novo usuário clicando em "Novo Usuário".</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginação --}}
            @if($users->hasPages())
                <div class="p-6 border-t border-slate-100 bg-slate-50/40">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        {{-- ========================================== --}}
        {{-- MODAL: RESUMO GERAL DO USUÁRIO (EXPANDIDO) --}}
        {{-- ========================================== --}}
        <div x-show="showSummaryModal" x-cloak
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-md flex items-center justify-center p-3 sm:p-6"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95">

            <div class="bg-white rounded-3xl max-w-5xl w-full shadow-2xl border border-slate-100 overflow-hidden relative flex flex-col max-h-[92vh]"
                @click.away="showSummaryModal = false">

                {{-- Header Moderno do Modal --}}
                <div class="p-4 sm:p-6 lg:p-8 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-white relative overflow-hidden">
                    <div class="absolute -right-16 -bottom-16 size-64 rounded-full bg-primary/10 blur-2xl pointer-events-none"></div>
                    <div class="absolute -left-12 -top-12 size-48 rounded-full bg-emerald-500/10 blur-xl pointer-events-none"></div>

                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 sm:gap-6 relative z-10">
                        {{-- Perfil do Usuário --}}
                        <div class="flex items-center gap-3.5 sm:gap-5 min-w-0">
                            <div class="size-12 xs:size-16 sm:size-20 rounded-2xl bg-gradient-to-tr from-primary to-blue-500 p-0.5 shadow-xl shadow-primary/20 shrink-0">
                                <div class="w-full h-full bg-slate-900 rounded-[14px] flex items-center justify-center font-black text-xl xs:text-2xl sm:text-3xl text-white">
                                    <span x-text="summaryData?.user?.name ? summaryData.user.name.charAt(0).toUpperCase() : 'U'"></span>
                                </div>
                            </div>
                            <div class="space-y-1.5 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-lg sm:text-2xl font-black text-white truncate max-w-[200px] xs:max-w-sm sm:max-w-md"
                                        x-text="summaryData?.user?.name || 'Carregando...'"></h3>
                                    
                                    {{-- Segmento do Cliente --}}
                                    <template x-if="summaryData?.user?.segment">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider shadow-sm"
                                            :class="{
                                                'bg-amber-400/20 text-amber-300 border border-amber-400/30': summaryData?.user?.segment_color === 'amber',
                                                'bg-emerald-400/20 text-emerald-300 border border-emerald-400/30': summaryData?.user?.segment_color === 'emerald',
                                                'bg-blue-400/20 text-blue-300 border border-blue-400/30': summaryData?.user?.segment_color === 'blue',
                                                'bg-purple-400/20 text-purple-300 border border-purple-400/30': summaryData?.user?.segment_color === 'purple',
                                                'bg-white/10 text-slate-300 border border-white/10': summaryData?.user?.segment_color === 'slate'
                                            }"
                                            x-text="summaryData.user.segment">
                                        </span>
                                    </template>
                                </div>

                                {{-- Pílulas de Contato Rápido --}}
                                <div class="flex items-center gap-2 sm:gap-3 flex-wrap text-xs text-slate-300">
                                    <div class="flex items-center gap-1.5 bg-white/5 px-2.5 py-1 rounded-lg border border-white/10 max-w-full">
                                        <span class="material-symbols-outlined text-sm text-slate-400">mail</span>
                                        <span class="font-medium truncate max-w-[140px] xs:max-w-[180px] sm:max-w-[220px]" x-text="summaryData?.user?.email || ''"></span>
                                        <button type="button" @click="copyText(summaryData?.user?.email, 'email')"
                                            class="text-slate-400 hover:text-white transition-colors ml-1"
                                            :title="copiedField === 'email' ? 'Copiado!' : 'Copiar E-mail'">
                                            <span class="material-symbols-outlined text-xs" x-text="copiedField === 'email' ? 'check' : 'content_copy'"></span>
                                        </button>
                                    </div>

                                    <template x-if="summaryData?.user?.cpf && summaryData?.user?.cpf !== 'Não informado'">
                                        <div class="flex items-center gap-1.5 bg-white/5 px-2.5 py-1 rounded-lg border border-white/10">
                                            <span class="material-symbols-outlined text-sm text-slate-400">badge</span>
                                            <span class="font-mono font-medium text-xs" x-text="summaryData.user.cpf"></span>
                                            <button type="button" @click="copyText(summaryData?.user?.raw_cpf, 'cpf')"
                                                class="text-slate-400 hover:text-white transition-colors ml-1"
                                                :title="copiedField === 'cpf' ? 'Copiado!' : 'Copiar CPF'">
                                                <span class="material-symbols-outlined text-xs" x-text="copiedField === 'cpf' ? 'check' : 'content_copy'"></span>
                                            </button>
                                        </div>
                                    </template>

                                    <template x-for="role in (summaryData?.user?.roles || [])" :key="role">
                                        <span class="px-2 py-0.5 rounded-md bg-primary/20 text-blue-300 border border-primary/30 text-[10px] font-black uppercase tracking-wider" x-text="role"></span>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- Ações no Topo do Modal --}}
                        <div class="flex items-center gap-2 flex-wrap self-end lg:self-center">
                            <template x-if="summaryData?.user?.clean_phone">
                                <button type="button" @click="openWhatsApp()"
                                    class="px-3.5 py-2 bg-[#25D366] hover:bg-[#20bd5a] text-white rounded-xl text-xs font-black transition-all flex items-center gap-1.5 shadow-lg shadow-emerald-500/20 active:scale-95">
                                    <svg class="size-4 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.72 1.144 3.763 1.748 5.8 1.748h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                    </svg>
                                    <span>WhatsApp</span>
                                </button>
                            </template>

                            <template x-if="summaryData?.user?.edit_url">
                                <a :href="summaryData.user.edit_url"
                                    class="px-3.5 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 border border-white/10"
                                    title="Editar Dados do Usuário">
                                    <span class="material-symbols-outlined text-sm">edit</span>
                                    <span>Editar</span>
                                </a>
                            </template>

                            <button type="button" @click="showSummaryModal = false"
                                class="size-9 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors border border-white/10"
                                title="Fechar Modal">
                                <span class="material-symbols-outlined text-lg">close</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Loading Skeleton --}}
                <div x-show="summaryLoading" class="p-16 text-center space-y-4">
                    <div class="size-12 rounded-full border-4 border-slate-200 border-t-primary animate-spin mx-auto"></div>
                    <p class="text-sm font-bold text-slate-500">Buscando resumo geral e histórico do usuário...</p>
                </div>

                {{-- Conteúdo do Modal --}}
                <div x-show="!summaryLoading && summaryData" class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 space-y-6 custom-scrollbar">
                    
                    {{-- 4 Cards de Métricas Principais --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100/80 shadow-sm flex flex-col justify-between">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Total de Pedidos</span>
                                <span class="material-symbols-outlined text-slate-400 text-lg">shopping_cart</span>
                            </div>
                            <div class="mt-2">
                                <span class="text-2xl font-black text-slate-900" x-text="summaryData?.stats?.orders_count ?? 0"></span>
                                <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                                    <span class="text-emerald-600 font-bold" x-text="(summaryData?.stats?.paid_orders_count ?? 0) + ' pagos'"></span>
                                    <template x-if="(summaryData?.stats?.pending_orders_count ?? 0) > 0">
                                        <span class="text-amber-600 font-bold" x-text="' • ' + summaryData.stats.pending_orders_count + ' pendentes'"></span>
                                    </template>
                                </p>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100 shadow-sm flex flex-col justify-between">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-black uppercase tracking-wider text-emerald-600">Total Faturado (LTV)</span>
                                <span class="material-symbols-outlined text-emerald-600 text-lg">payments</span>
                            </div>
                            <div class="mt-2">
                                <span class="text-2xl font-black text-emerald-700 font-mono" x-text="'R$ ' + (summaryData?.stats?.total_spent ?? '0,00')"></span>
                                <p class="text-[11px] text-emerald-600 font-medium mt-0.5">receita confirmada</p>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-blue-50/60 border border-blue-100 shadow-sm flex flex-col justify-between">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-black uppercase tracking-wider text-blue-600">Ticket Médio</span>
                                <span class="material-symbols-outlined text-blue-600 text-lg">trending_up</span>
                            </div>
                            <div class="mt-2">
                                <span class="text-2xl font-black text-blue-700 font-mono" x-text="'R$ ' + (summaryData?.stats?.average_ticket ?? '0,00')"></span>
                                <p class="text-[11px] text-blue-600 font-medium mt-0.5">média por pedido pago</p>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-purple-50/60 border border-purple-100 shadow-sm flex flex-col justify-between">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-black uppercase tracking-wider text-purple-600">Último Acesso</span>
                                <span class="material-symbols-outlined text-purple-600 text-lg">schedule</span>
                            </div>
                            <div class="mt-2">
                                <span class="text-sm font-black text-purple-900 truncate block" x-text="summaryData?.user?.last_login_diff || 'Nunca'"></span>
                                <p class="text-[11px] text-purple-600 truncate mt-0.5" x-text="summaryData?.user?.last_login || ''"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Abas de Navegação --}}
                    <div class="flex items-center gap-2 border-b border-slate-100 pb-2 overflow-x-auto scrollbar-hide">
                        <button type="button" @click="activeSummaryTab = 'orders'"
                            class="px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2 shrink-0"
                            :class="activeSummaryTab === 'orders' 
                                ? 'bg-primary text-white shadow-md shadow-primary/20' 
                                : 'text-slate-500 hover:text-slate-900 hover:bg-slate-100'">
                            <span class="material-symbols-outlined text-base">receipt_long</span>
                            <span>Pedidos (<span x-text="summaryData?.orders?.length || 0"></span>)</span>
                        </button>

                        <button type="button" @click="activeSummaryTab = 'info'"
                            class="px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2 shrink-0"
                            :class="activeSummaryTab === 'info' 
                                ? 'bg-primary text-white shadow-md shadow-primary/20' 
                                : 'text-slate-500 hover:text-slate-900 hover:bg-slate-100'">
                            <span class="material-symbols-outlined text-base">badge</span>
                            <span>Perfil & Documentos</span>
                        </button>

                        <button type="button" @click="activeSummaryTab = 'addresses'"
                            class="px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2 shrink-0"
                            :class="activeSummaryTab === 'addresses' 
                                ? 'bg-primary text-white shadow-md shadow-primary/20' 
                                : 'text-slate-500 hover:text-slate-900 hover:bg-slate-100'">
                            <span class="material-symbols-outlined text-base">pin_drop</span>
                            <span>Endereços (<span x-text="summaryData?.addresses?.length || 0"></span>)</span>
                        </button>

                        <button type="button" @click="activeSummaryTab = 'whatsapp'"
                            class="px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2 shrink-0"
                            :class="activeSummaryTab === 'whatsapp' 
                                ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' 
                                : 'text-slate-500 hover:text-emerald-700 hover:bg-emerald-50'">
                            <span class="material-symbols-outlined text-base">chat</span>
                            <span>Atendimento Rápido</span>
                        </button>
                    </div>

                    {{-- ABA 1: PEDIDOS COMPRADOS --}}
                    <div x-show="activeSummaryTab === 'orders'" class="space-y-4">
                        <template x-if="!summaryData?.orders || summaryData.orders.length === 0">
                            <div class="p-12 text-center bg-slate-50 rounded-3xl border border-slate-100 space-y-3">
                                <div class="size-16 rounded-2xl bg-white shadow-sm border border-slate-100 flex items-center justify-center text-slate-300 mx-auto">
                                    <span class="material-symbols-outlined text-3xl">shopping_basket</span>
                                </div>
                                <h4 class="text-base font-black text-slate-800">Nenhum pedido realizado ainda</h4>
                                <p class="text-xs text-slate-400 max-w-sm mx-auto">Este cliente ainda não efetuou compras na loja virtual. Você pode entrar em contato via WhatsApp e oferecer um cupom exclusivo para incentivá-lo!</p>
                                <template x-if="summaryData?.user?.clean_phone">
                                    <button type="button" @click="activeSummaryTab = 'whatsapp'"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-black text-xs rounded-xl transition-colors mt-2">
                                        <span class="material-symbols-outlined text-sm">local_offer</span>
                                        <span>Enviar Cupom no WhatsApp</span>
                                    </button>
                                </template>
                            </div>
                        </template>

                        <template x-for="order in (summaryData?.orders || [])" :key="order.id">
                            <div class="p-5 bg-slate-50/80 hover:bg-slate-50 rounded-2xl border border-slate-200/80 transition-all space-y-4 shadow-sm">
                                {{-- Cabeçalho do Pedido --}}
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200/60 pb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="size-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-black text-xs font-mono shadow-sm">
                                            #<span x-text="order.order_number"></span>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-bold text-slate-900" x-text="order.created_at"></span>
                                                <span class="text-[10px] text-slate-400" x-text="'(' + order.created_at_diff + ')'"></span>
                                            </div>
                                            <div class="flex items-center gap-2 mt-0.5 text-[11px] text-slate-500">
                                                <span class="font-bold uppercase tracking-wider text-slate-600" x-text="order.payment_method"></span>
                                                <span>•</span>
                                                <span x-text="order.shipping_modalidade"></span>
                                                <template x-if="order.tracking_code">
                                                    <span class="text-primary font-bold" x-text="' • Rastreio: ' + order.tracking_code"></span>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border"
                                            :class="{
                                                'bg-emerald-50 text-emerald-700 border-emerald-200': order.status === 'paid' || order.status === 'delivered',
                                                'bg-blue-50 text-blue-700 border-blue-200': order.status === 'shipped' || order.status === 'processing',
                                                'bg-amber-50 text-amber-700 border-amber-200': order.status === 'pending',
                                                'bg-red-50 text-red-700 border-red-200': order.status === 'cancelled',
                                            }"
                                            x-text="order.status_label">
                                        </span>
                                        <span class="text-base font-black text-slate-900 font-mono" x-text="'R$ ' + order.total_amount"></span>
                                        <a :href="order.view_url" target="_blank"
                                            class="px-3 py-1.5 bg-white border border-slate-200 hover:border-slate-300 rounded-xl text-xs font-bold text-slate-700 flex items-center gap-1.5 shadow-sm transition-all hover:bg-slate-50"
                                            title="Ver Detalhes do Pedido">
                                            <span>Ver Pedido</span>
                                            <span class="material-symbols-outlined text-sm">open_in_new</span>
                                        </a>
                                    </div>
                                </div>

                                {{-- Itens do Pedido --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                                    <template x-for="item in order.items" :key="item.name + item.size">
                                        <div class="flex items-center gap-3 p-2.5 bg-white rounded-xl border border-slate-100 shadow-sm">
                                            <template x-if="item.image">
                                                <img :src="item.image" class="size-11 rounded-lg object-cover bg-slate-50 flex-shrink-0 border border-slate-100">
                                            </template>
                                            <template x-if="!item.image">
                                                <div class="size-11 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 flex-shrink-0">
                                                    <span class="material-symbols-outlined text-sm">steps</span>
                                                </div>
                                            </template>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs font-bold text-slate-800 truncate" x-text="item.name"></p>
                                                <div class="flex items-center gap-1.5 text-[10px] text-slate-500 font-semibold mt-0.5">
                                                    <span class="bg-slate-100 px-1.5 py-0.5 rounded text-slate-700 font-bold" x-text="item.quantity + 'x'"></span>
                                                    <template x-if="item.size">
                                                        <span class="text-primary font-bold" x-text="'Tam ' + item.size"></span>
                                                    </template>
                                                    <template x-if="item.color_name">
                                                        <span class="truncate max-w-[80px]" x-text="'• ' + item.color_name"></span>
                                                    </template>
                                                </div>
                                            </div>
                                            <span class="text-xs font-bold text-slate-900 font-mono" x-text="'R$ ' + item.price"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- ABA 2: DADOS CADASTRAIS & PERFIL --}}
                    <div x-show="activeSummaryTab === 'info'" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                <span class="text-[10px] uppercase font-black text-slate-400 block tracking-wider">Nome Completo</span>
                                <span class="text-sm font-bold text-slate-900 mt-1 block" x-text="summaryData?.user?.name"></span>
                            </div>

                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] uppercase font-black text-slate-400 block tracking-wider">E-mail</span>
                                    <span class="text-sm font-bold text-slate-900 mt-1 block truncate max-w-[200px]" x-text="summaryData?.user?.email"></span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button type="button" @click="copyText(summaryData?.user?.email, 'email_tab')"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 transition-colors"
                                        title="Copiar E-mail">
                                        <span class="material-symbols-outlined text-sm" x-text="copiedField === 'email_tab' ? 'check' : 'content_copy'"></span>
                                    </button>
                                    <a :href="'mailto:' + summaryData?.user?.email"
                                        class="p-1.5 rounded-lg text-primary hover:bg-primary/10 transition-colors"
                                        title="Enviar E-mail">
                                        <span class="material-symbols-outlined text-sm">send</span>
                                    </a>
                                </div>
                            </div>

                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] uppercase font-black text-slate-400 block tracking-wider">CPF</span>
                                    <span class="text-sm font-bold font-mono text-slate-900 mt-1 block" x-text="summaryData?.user?.cpf"></span>
                                </div>
                                <template x-if="summaryData?.user?.raw_cpf">
                                    <button type="button" @click="copyText(summaryData.user.raw_cpf, 'cpf_tab')"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 transition-colors"
                                        title="Copiar CPF">
                                        <span class="material-symbols-outlined text-sm" x-text="copiedField === 'cpf_tab' ? 'check' : 'content_copy'"></span>
                                    </button>
                                </template>
                            </div>

                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] uppercase font-black text-slate-400 block tracking-wider">Telefone / WhatsApp</span>
                                    <span class="text-sm font-bold text-slate-900 mt-1 block" x-text="summaryData?.user?.phone"></span>
                                </div>
                                <template x-if="summaryData?.user?.clean_phone">
                                    <button type="button" @click="openWhatsApp()"
                                        class="px-2.5 py-1.5 bg-[#25D366] hover:bg-[#20bd5a] text-white rounded-xl text-xs font-bold transition-all flex items-center gap-1 shadow-sm">
                                        <span class="material-symbols-outlined text-xs">chat</span>
                                        <span>Chamar</span>
                                    </button>
                                </template>
                            </div>

                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                <span class="text-[10px] uppercase font-black text-slate-400 block tracking-wider">Cliente Desde</span>
                                <span class="text-sm font-bold text-slate-900 mt-1 block" x-text="summaryData?.user?.created_at"></span>
                                <span class="text-[11px] text-slate-400" x-text="summaryData?.user?.created_at_diff"></span>
                            </div>

                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                <span class="text-[10px] uppercase font-black text-slate-400 block tracking-wider">Último Acesso Registrado</span>
                                <span class="text-sm font-bold text-slate-900 mt-1 block" x-text="summaryData?.user?.last_login"></span>
                                <span class="text-[11px] text-slate-400" x-text="'IP: ' + summaryData?.user?.last_login_ip"></span>
                            </div>
                        </div>

                        {{-- Card Gateway Asaas & Sistema --}}
                        <div class="p-4 sm:p-5 bg-gradient-to-r from-slate-50 to-blue-50/40 rounded-2xl border border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary text-base">cloud_sync</span>
                                    <span class="text-xs font-black uppercase tracking-wider text-slate-800">Integração Asaas Pagamentos</span>
                                </div>
                                <p class="text-xs text-slate-500">
                                    ID do Cliente: <strong class="font-mono text-slate-800" x-text="summaryData?.user?.asaas_customer_id || 'Não registrado'"></strong>
                                </p>
                            </div>
                            <template x-if="summaryData?.user?.edit_url">
                                <a :href="summaryData.user.edit_url"
                                    class="px-4 py-2 bg-white border border-slate-200 hover:border-slate-300 rounded-xl text-xs font-bold text-slate-700 flex items-center gap-1.5 shadow-sm transition-all hover:bg-slate-50 self-start sm:self-auto">
                                    <span class="material-symbols-outlined text-sm">manage_accounts</span>
                                    <span>Editar Permissões & Senha</span>
                                </a>
                            </template>
                        </div>
                    </div>

                    {{-- ABA 3: ENDEREÇOS DE ENTREGA --}}
                    <div x-show="activeSummaryTab === 'addresses'" class="space-y-3">
                        <template x-if="!summaryData?.addresses || summaryData.addresses.length === 0">
                            <div class="p-12 text-center bg-slate-50 rounded-3xl border border-slate-100 space-y-2">
                                <div class="size-16 rounded-2xl bg-white shadow-sm border border-slate-100 flex items-center justify-center text-slate-300 mx-auto">
                                    <span class="material-symbols-outlined text-3xl">home_pin</span>
                                </div>
                                <h4 class="text-base font-black text-slate-800">Nenhum endereço cadastrado</h4>
                                <p class="text-xs text-slate-400 max-w-sm mx-auto">O cliente ainda não salvou endereços para entrega. Os endereços são gravados automaticamente durante o checkout.</p>
                            </div>
                        </template>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <template x-for="addr in (summaryData?.addresses || [])" :key="addr.id">
                                <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100 flex flex-col justify-between gap-4">
                                    <div class="flex items-start gap-3.5">
                                        <div class="size-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                                            <span class="material-symbols-outlined text-lg">pin_drop</span>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                <span class="font-black text-slate-900 text-sm" x-text="addr.label || 'Endereço'"></span>
                                                <template x-if="addr.is_default">
                                                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-black uppercase">Principal</span>
                                                </template>
                                            </div>
                                            <p class="text-xs text-slate-700 font-medium mt-1 leading-relaxed" x-text="addr.full_address"></p>
                                            <p class="text-[11px] text-slate-400 mt-1" x-text="'Destinatário: ' + addr.recipient_name"></p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 pt-2 border-t border-slate-200/60">
                                        <button type="button" @click="copyText(addr.full_address, 'addr_' + addr.id)"
                                            class="px-3 py-1.5 bg-white border border-slate-200 hover:border-slate-300 rounded-xl text-xs font-bold text-slate-700 flex items-center gap-1 transition-all"
                                            :title="copiedField === 'addr_' + addr.id ? 'Copiado!' : 'Copiar Endereço'">
                                            <span class="material-symbols-outlined text-xs" x-text="copiedField === 'addr_' + addr.id ? 'check' : 'content_copy'"></span>
                                            <span x-text="copiedField === 'addr_' + addr.id ? 'Copiado' : 'Copiar'"></span>
                                        </button>
                                        <a :href="addr.maps_url" target="_blank"
                                            class="px-3 py-1.5 bg-white border border-slate-200 hover:border-slate-300 rounded-xl text-xs font-bold text-primary flex items-center gap-1 transition-all hover:bg-slate-50">
                                            <span class="material-symbols-outlined text-xs">map</span>
                                            <span>Ver no Maps</span>
                                        </a>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- ABA 4: ATENDIMENTO RÁPIDO & TEMPLATES WHATSAPP --}}
                    <div x-show="activeSummaryTab === 'whatsapp'" class="space-y-4">
                        <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-100 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="size-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-xl">forum</span>
                                </div>
                                <div>
                                    <h4 class="text-sm font-black text-emerald-950">Atendimento 1-Clique via WhatsApp</h4>
                                    <p class="text-xs text-emerald-700">Envie mensagens personalizadas com o nome do cliente pronto para envio.</p>
                                </div>
                            </div>
                            <span class="text-xs font-mono font-bold text-emerald-800" x-text="summaryData?.user?.phone || 'Sem telefone'"></span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                            {{-- Template 1: Boas-Vindas --}}
                            <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100 flex flex-col justify-between gap-4">
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2 text-primary font-black text-xs uppercase tracking-wider">
                                        <span class="material-symbols-outlined text-base">waving_hand</span>
                                        <span>Boas-Vindas</span>
                                    </div>
                                    <p class="text-xs text-slate-600 leading-relaxed italic">
                                        "Olá <span class="font-bold" x-text="summaryData?.user?.name"></span>, tudo bem? Seja muito bem-vindo(a) à Denner Tênis! Como podemos te ajudar a encontrar seu tênis ideal hoje?"
                                    </p>
                                </div>
                                <button type="button"
                                    @click="openWhatsApp(`Olá ${summaryData?.user?.name || ''}, tudo bem? Seja muito bem-vindo(a) à Denner Tênis! Como podemos te ajudar a encontrar seu tênis ideal hoje?`)"
                                    class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5">
                                    <span class="material-symbols-outlined text-sm">send</span>
                                    <span>Enviar Mensagem</span>
                                </button>
                            </div>

                            {{-- Template 2: Cupom de Desconto --}}
                            <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100 flex flex-col justify-between gap-4">
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2 text-emerald-600 font-black text-xs uppercase tracking-wider">
                                        <span class="material-symbols-outlined text-base">local_offer</span>
                                        <span>Cupom Exclusivo</span>
                                    </div>
                                    <p class="text-xs text-slate-600 leading-relaxed italic">
                                        "Olá <span class="font-bold" x-text="summaryData?.user?.name"></span>! Liberamos uma condição especial de 10% OFF para você em nossa loja de tênis. Use o cupom BEMVINDO10!"
                                    </p>
                                </div>
                                <button type="button"
                                    @click="openWhatsApp(`Olá ${summaryData?.user?.name || ''}! Liberamos uma condição especial de 10% OFF para você em nossa loja de tênis. Use o cupom BEMVINDO10!`)"
                                    class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 shadow-sm">
                                    <span class="material-symbols-outlined text-sm">send</span>
                                    <span>Enviar Cupom</span>
                                </button>
                            </div>

                            {{-- Template 3: Suporte a Pedido --}}
                            <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100 flex flex-col justify-between gap-4">
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2 text-amber-600 font-black text-xs uppercase tracking-wider">
                                        <span class="material-symbols-outlined text-base">support_agent</span>
                                        <span>Suporte ao Cliente</span>
                                    </div>
                                    <p class="text-xs text-slate-600 leading-relaxed italic">
                                        "Olá <span class="font-bold" x-text="summaryData?.user?.name"></span>, aqui é do suporte Denner Tênis. Notamos seu interesse em nossa loja e estamos à disposição para qualquer dúvida."
                                    </p>
                                </div>
                                <button type="button"
                                    @click="openWhatsApp(`Olá ${summaryData?.user?.name || ''}, aqui é do suporte Denner Tênis. Notamos seu interesse em nossa loja e estamos à disposição para qualquer dúvida!`)"
                                    class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5">
                                    <span class="material-symbols-outlined text-sm">send</span>
                                    <span>Enviar Suporte</span>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Footer do Modal --}}
                <div class="p-4 sm:p-6 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                    <p class="text-xs text-slate-400">Pressione <kbd class="px-1.5 py-0.5 bg-white border border-slate-200 rounded text-[10px] font-mono">ESC</kbd> para fechar</p>
                    <button type="button" @click="showSummaryModal = false"
                        class="px-6 py-2.5 bg-slate-900 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition-colors shadow-sm">
                        Fechar
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-admin-layout>
