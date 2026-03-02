<x-customer-layout title="Dados Pessoais" active="profile">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Dados Pessoais</h1>
        <p class="text-slate-500 mt-1 font-medium">Gerencie suas informações básicas e segurança da conta.</p>
    </div>

    @if (session('status') === 'profile-updated')
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center gap-3 font-bold text-sm shadow-sm transition-all">
            <span class="material-symbols-outlined">check_circle</span>
            Perfil atualizado com sucesso!
        </div>
    @endif

    @if (session('status') === 'password-updated')
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center gap-3 font-bold text-sm shadow-sm transition-all">
            <span class="material-symbols-outlined">check_circle</span>
            Senha alterada com sucesso!
        </div>
    @endif

    <div class="space-y-8">
        <!-- Informações Básicas -->
        <form x-data method="post" action="{{ route('customer.profile.update') }}" class="space-y-8">
            @csrf
            @method('patch')

            <section
                class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 md:p-8 shadow-sm">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                        <span class="material-symbols-outlined">badge</span>
                    </div>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">Informações Básicas</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300 ml-1" for="name">Nome
                            Completo</label>
                        <input name="name"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all @error('name') border-red-500 @enderror"
                            id="name" type="text" value="{{ old('name', Auth::user()->name) }}" required />
                        @error('name') <p class="text-xs text-red-500 mt-1 ml-1 font-bold">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300 ml-1"
                            for="email">E-mail</label>
                        <input name="email"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all @error('email') border-red-500 @enderror"
                            id="email" type="email" value="{{ old('email', Auth::user()->email) }}" required />
                        @error('email') <p class="text-xs text-red-500 mt-1 ml-1 font-bold">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300 ml-1" for="cpf">CPF</label>
                        <input name="cpf" x-mask="999.999.999-99"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all @error('cpf') border-red-500 @enderror"
                            id="cpf" type="text" value="{{ old('cpf', Auth::user()->cpf) }}"
                            placeholder="000.000.000-00" />
                        @error('cpf') <p class="text-xs text-red-500 mt-1 ml-1 font-bold">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300 ml-1"
                            for="phone">Telefone</label>
                        <input name="phone" x-mask="(99) 99999-9999"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all @error('phone') border-red-500 @enderror"
                            id="phone" type="tel" value="{{ old('phone', Auth::user()->phone) }}"
                            placeholder="(00) 00000-0000" />
                        @error('phone') <p class="text-xs text-red-500 mt-1 ml-1 font-bold">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="flex justify-end mt-8">
                    <button
                        class="bg-primary hover:bg-emerald-600 text-white font-black py-4 px-10 rounded-2xl transition-all shadow-xl shadow-primary/20 flex items-center gap-2 hover:scale-105"
                        type="submit">
                        <span class="material-symbols-outlined text-xl">save</span>
                        Salvar Alterações
                    </button>
                </div>
            </section>
        </form>

        <!-- Segurança -->
        <form method="post" action="{{ route('customer.password.update') }}">
            @csrf
            @method('put')

            <section
                class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-6 md:p-8 shadow-sm">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center">
                        <span class="material-symbols-outlined">lock</span>
                    </div>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">Segurança</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300 ml-1"
                            for="current_password">Senha Atual</label>
                        <input name="current_password"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all @error('current_password', 'updatePassword') border-red-500 @enderror"
                            id="current_password" type="password" required autocomplete="current-password" />
                        @error('current_password', 'updatePassword') <p
                        class="text-xs text-red-500 mt-1 ml-1 font-bold">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300 ml-1" for="password">Nova
                            Senha</label>
                        <input name="password"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all @error('password', 'updatePassword') border-red-500 @enderror"
                            id="password" type="password" required autocomplete="new-password" />
                        @error('password', 'updatePassword') <p class="text-xs text-red-500 mt-1 ml-1 font-bold">
                            {{ $message }}
                        </p> @enderror
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700 dark:text-slate-300 ml-1"
                            for="password_confirmation">Confirmar Nova Senha</label>
                        <input name="password_confirmation"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-700 rounded-2xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all @error('password_confirmation', 'updatePassword') border-red-500 @enderror"
                            id="password_confirmation" type="password" required autocomplete="new-password" />
                        @error('password_confirmation', 'updatePassword') <p
                        class="text-xs text-red-500 mt-1 ml-1 font-bold">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end">
                    <button
                        class="bg-slate-900 text-white dark:bg-white dark:text-slate-900 font-black py-4 px-10 rounded-2xl transition-all shadow-xl hover:scale-105 flex items-center gap-2"
                        type="submit">
                        <span class="material-symbols-outlined text-lg">key</span>
                        Alterar Senha
                    </button>
                </div>
            </section>
        </form>
    </div>
</x-customer-layout>