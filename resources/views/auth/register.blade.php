<x-guest-layout>
    <div class="flex min-h-screen">
        {{-- Side Image --}}
        <div class="hidden lg:block lg:w-1/2 relative bg-slate-950">
            <img alt="Tênis de Corrida e Maratona" class="absolute inset-0 w-full h-full object-cover"
                src="https://images.unsplash.com/photo-1571008887538-b36bb32f4571?auto=format&fit=crop&q=80&w=2000">
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-black/20"></div>
            <div class="absolute bottom-16 left-16 right-16 text-white z-20">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white text-[11px] font-black uppercase tracking-widest mb-4">
                    <span class="material-symbols-outlined text-sm text-primary">directions_run</span>
                    Comunidade de Corredores
                </div>
                <blockquote class="text-3xl lg:text-4xl font-black leading-tight drop-shadow-md">
                    "Acesso antecipado aos melhores tênis de corrida, maratona e treino diário."
                </blockquote>
                <p class="mt-4 font-bold uppercase tracking-[0.2em] text-xs opacity-80">
                    — {{ $appSettings['store_name'] ?? 'DENNER Tênis' }} Running Club
                </p>
            </div>
        </div>

        <div
            class="w-full lg:w-1/2 flex items-center justify-center p-6 lg:p-12 bg-white dark:bg-slate-950 overflow-y-auto">
            <div class="w-full max-w-md py-2">
                <div class="flex items-center gap-2 mb-6 lg:mb-8">
                    @if($appSettings['store_logo'])
                        <img src="{{ asset('storage/' . $appSettings['store_logo']) }}" class="h-8 w-auto object-contain"
                            style="height: {{ ($appSettings['store_logo_size'] ?? 100) * 0.3 }}px; max-height: 96px;"
                            alt="{{ $appSettings['store_name'] }}">
                    @else
                        <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center text-white">
                            <span
                                class="material-symbols-outlined text-2xl">{{ $appSettings['store_icon'] ?? 'chair' }}</span>
                        </div>
                        <span class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white uppercase">
                            {{ $appSettings['store_name'] ?? 'Verve' }}<span class="text-primary">Home</span>
                        </span>
                    @endif
                </div>

                <div class="mb-6 lg:mb-8">
                    <h1 class="text-3xl lg:text-4xl font-bold text-slate-900 dark:text-white mb-2">Criar conta</h1>
                    <p class="text-slate-500 dark:text-slate-400 text-sm">Junte-se a nós para ter acesso a coleções
                        exclusivas e ofertas especiais.</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    {{-- Name --}}
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 dark:text-slate-300" for="name">Nome
                            Completo</label>
                        <input
                            class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white placeholder:text-slate-400 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none"
                            id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                            placeholder="Como devemos te chamar?" type="text" />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>

                    {{-- Email --}}
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 dark:text-slate-300"
                            for="email">E-mail</label>
                        <input
                            class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white placeholder:text-slate-400 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none"
                            id="email" name="email" value="{{ old('email') }}" required placeholder="seu@email.com"
                            type="email" />
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    {{-- Password --}}
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 dark:text-slate-300"
                            for="password">Senha</label>
                        <input
                            class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white placeholder:text-slate-400 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none"
                            id="password" name="password" required autocomplete="new-password"
                            placeholder="Mínimo 8 caracteres" type="password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    {{-- Confirm Password --}}
                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 dark:text-slate-300"
                            for="password_confirmation">Confirmar Senha</label>
                        <input
                            class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white placeholder:text-slate-400 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none"
                            id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                            placeholder="Repita sua senha" type="password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                    </div>

                    <button type="submit"
                        class="w-full bg-primary hover:bg-emerald-600 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-primary/25 transition-all active:scale-[0.98] mt-2">
                        Criar minha conta
                    </button>
                </form>

                <p class="mt-6 lg:mt-8 text-center text-sm text-slate-500 dark:text-slate-400">
                    Já possui uma conta?
                    <a class="font-bold text-slate-900 dark:text-white hover:text-primary transition-colors"
                        href="{{ route('login') }}">Fazer login</a>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>