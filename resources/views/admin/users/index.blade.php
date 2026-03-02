<x-admin-layout>
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Usuários</h1>
                <p class="text-slate-500 mt-1">Gerencie os usuários e permissões do sistema.</p>
            </div>
            <a href="{{ route('admin.users.create') }}"
                class="inline-flex items-center justify-center px-5 py-3 bg-primary text-white font-semibold rounded-xl transition-all shadow-lg shadow-primary/20 gap-2">
                <span class="material-symbols-outlined text-xl">person_add</span>
                Novo Usuário
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

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-red-500">error</span>
                    <p class="text-red-700 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Users Table -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-50/50 border-b border-slate-100 uppercase text-[11px] font-bold text-slate-500 tracking-wider">
                            <th class="px-6 py-4">Usuário</th>
                            <th class="px-6 py-4">CPF / Telefone</th>
                            <th class="px-6 py-4">Função</th>
                            <th class="px-6 py-4 text-center">Data Cadastro</th>
                            <th class="px-6 py-4 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $user)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900 group-hover:text-primary transition-colors">
                                                {{ $user->name }}
                                            </p>
                                            <p class="text-xs text-slate-500">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <p class="text-sm font-medium text-slate-700">{{ $user->cpf ?? '---' }}</p>
                                    <p class="text-xs text-slate-400 font-medium">{{ $user->phone ?? '---' }}</p>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($user->roles as $role)
                                            <span
                                                class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold">{{ $role->name }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center text-sm font-medium text-slate-600">
                                    {{ $user->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                            class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-primary/10 hover:text-primary transition-all border border-slate-100">
                                            <span class="material-symbols-outlined text-xl">edit</span>
                                        </a>
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                            onsubmit="return confirm('Tem certeza que deseja excluir este usuário?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition-all border border-slate-100">
                                                <span class="material-symbols-outlined text-xl">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center">
                                            <span class="material-symbols-outlined text-4xl text-slate-300">group</span>
                                        </div>
                                        <h3 class="text-slate-900 font-bold">Nenhum usuário encontrado</h3>
                                        <p class="text-slate-500 text-sm">Os usuários cadastrados aparecerão aqui.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div class="p-6 border-t border-slate-100 bg-slate-50/30">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>