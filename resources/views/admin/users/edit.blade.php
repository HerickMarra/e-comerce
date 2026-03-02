<x-admin-layout>
    <div class="max-w-4xl mx-auto space-y-8">
        <!-- Header -->
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.users.index') }}"
                class="w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-all">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Editar Usuário</h1>
                <p class="text-slate-500 mt-1">Atualize as informações de acesso do usuário.</p>
            </div>
        </div>

        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Main Info -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 space-y-8">
                        <h2 class="text-xl font-bold text-slate-900 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                                <span class="material-symbols-outlined">person</span>
                            </div>
                            Informações Pessoais
                        </h2>

                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Nome Completo</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                    class="w-full bg-slate-50 border-slate-200 rounded-2xl focus:ring-primary/50 focus:border-primary transition-all font-medium py-4 px-6 border-2"
                                    placeholder="Ex: João Silva">
                                @error('name') <p class="mt-2 text-sm text-red-500 font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">E-mail</label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                        class="w-full bg-slate-50 border-slate-200 rounded-2xl focus:ring-primary/50 focus:border-primary transition-all font-medium py-4 px-6 border-2"
                                        placeholder="email@exemplo.com">
                                    @error('email') <p class="mt-2 text-sm text-red-500 font-medium">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">CPF (Opcional)</label>
                                    <input type="text" name="cpf" value="{{ old('cpf', $user->cpf) }}"
                                        class="w-full bg-slate-50 border-slate-200 rounded-2xl focus:ring-primary/50 focus:border-primary transition-all font-medium py-4 px-6 border-2"
                                        placeholder="000.000.000-00">
                                    @error('cpf') <p class="mt-2 text-sm text-red-500 font-medium">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Telefone (Opcional)</label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                                    class="w-full bg-slate-50 border-slate-200 rounded-2xl focus:ring-primary/50 focus:border-primary transition-all font-medium py-4 px-6 border-2"
                                    placeholder="(00) 00000-0000">
                                @error('phone') <p class="mt-2 text-sm text-red-500 font-medium">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 space-y-8">
                        <h2 class="text-xl font-bold text-slate-900 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                                <span class="material-symbols-outlined">lock</span>
                            </div>
                            Segurança
                        </h2>
                        
                        <p class="text-sm text-slate-500 -mt-4">Deixe em branco se não desejar alterar a senha.</p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Nova Senha</label>
                                <input type="password" name="password"
                                    class="w-full bg-slate-50 border-slate-200 rounded-2xl focus:ring-primary/50 focus:border-primary transition-all font-medium py-4 px-6 border-2">
                                @error('password') <p class="mt-2 text-sm text-red-500 font-medium">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Confirmar Nova Senha</label>
                                <input type="password" name="password_confirmation"
                                    class="w-full bg-slate-50 border-slate-200 rounded-2xl focus:ring-primary/50 focus:border-primary transition-all font-medium py-4 px-6 border-2">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Info -->
                <div class="space-y-6">
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 space-y-6">
                        <h2 class="text-xl font-bold text-slate-900 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                <span class="material-symbols-outlined">key</span>
                            </div>
                            Funções
                        </h2>

                        <div class="space-y-3">
                            @foreach($roles as $role)
                                <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-slate-50 hover:border-primary/20 transition-all cursor-pointer">
                                    <input type="checkbox" name="roles[]" value="{{ $role->name }}" 
                                        class="w-5 h-5 rounded-lg border-slate-300 text-primary focus:ring-primary/20"
                                        {{ in_array($role->name, old('roles', $user->roles->pluck('name')->toArray())) ? 'checked' : '' }}>
                                    <span class="font-bold text-slate-700">{{ $role->name }}</span>
                                </label>
                            @endforeach
                            @error('roles') <p class="mt-2 text-sm text-red-500 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <p class="text-xs text-slate-400 font-medium">Define as permissões que o usuário terá no sistema.</p>
                    </div>

                    <div class="pt-4 flex flex-col gap-3">
                        <button type="submit"
                            class="w-full py-5 bg-primary hover:bg-emerald-700 text-white font-black text-lg rounded-3xl transition-all shadow-2xl shadow-primary/20 flex items-center justify-center gap-3">
                            <span class="material-symbols-outlined">save</span>
                            Atualizar Usuário
                        </button>
                        <a href="{{ route('admin.users.index') }}"
                            class="w-full py-5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-3xl transition-all text-center">
                            Descartar Alterações
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-admin-layout>
