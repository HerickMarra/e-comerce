<x-guest-layout>
    <div class="flex min-h-screen">
        <div class="hidden lg:block lg:w-1/2 relative">
            <img alt="Sofá Moderno Verde Esmeralda em Sala de Estar" class="absolute inset-0 w-full h-full object-cover"
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuCzEdqerwLlfQVj3pgmP4XT1RKA4w4lUhUOKPq3RvtsIukr-A2ipBJvmKxmZNckjkfYeMKdA1RWBt7jnAcxxfozKF8EXKXyk_4njL6Qunk7_muNcZXAKeLx0QAcUJbwGptNiB3yQUwB97ugZc6LjwGyUN_bTv2YHWeX05VhiaUzly3HaavW8VxL52_0IkJU2P9amPBQ4QsoyQmJkhB-kHnRihBUp86h2usJIYIvnERZW9KCV5m2R66TaFxO6lQtF8qwZLEsAZBWcdg" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
            <div class="absolute bottom-16 left-16 right-16 text-white z-20">
                <blockquote class="text-4xl font-light leading-tight italic drop-shadow-md">
                    "Design is not just what it looks like and feels like. Design is how it works."
                </blockquote>
                <p class="mt-6 font-semibold uppercase tracking-[0.2em] text-sm opacity-90">—
                    {{ $appSettings['store_name'] ?? 'VerveHome' }} Interiors
                </p>
            </div>
        </div>
        <div
            class="w-full lg:w-1/2 flex items-center justify-center p-6 lg:p-12 bg-white dark:bg-slate-950 overflow-y-auto">
            <div class="w-full max-w-md py-2">
                <div class="flex items-center gap-2 mb-6 lg:mb-8">
                    @if($appSettings['store_logo'])
                        <img src="{{ asset('storage/' . $appSettings['store_logo']) }}" class="h-8 w-auto object-contain"
                            style="height: {{ ($appSettings['store_logo_size'] ?? 100) * 0.3 }}px; max-height: 48px;"
                            alt="{{ $appSettings['store_name'] }}">
                    @else
                        <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center text-white">
                            <span
                                class="material-symbols-outlined text-2xl">{{ $appSettings['store_icon'] ?? 'chair' }}</span>
                        </div>
                        <span class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white uppercase">
                            {{ $appSettings['store_name'] ?? config('app.name') }}
                        </span>
                    @endif
                </div>
                <div class="mb-6 lg:mb-8">
                    <h1 class="text-3xl lg:text-4xl font-bold text-slate-900 dark:text-white mb-2 leading-tight">
                        Bem-vindo de volta</h1>
                    <p class="text-slate-500 dark:text-slate-400 text-sm">Por favor, insira seus dados para entrar.</p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-4 lg:space-y-5">
                    @csrf

                    <div class="space-y-1.5">
                        <label class="text-sm font-semibold text-slate-700 dark:text-slate-300"
                            for="email">E-mail</label>
                        <input
                            class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white placeholder:text-slate-400 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none"
                            id="email" name="email" value="{{ old('email') }}" required autofocus
                            placeholder="nome@exemplo.com" type="email" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-300"
                                for="password">Senha</label>
                            @if (Route::has('password.request'))
                                <a class="text-xs font-semibold text-primary hover:text-emerald-600 transition-colors"
                                    href="{{ route('password.request') }}">Esqueceu a senha?</a>
                            @endif
                        </div>
                        <input
                            class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-slate-900 dark:text-white placeholder:text-slate-400 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none"
                            id="password" name="password" required autocomplete="current-password"
                            placeholder="••••••••" type="password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="flex items-center">
                        <input id="remember_me" type="checkbox"
                            class="rounded border-slate-300 text-primary shadow-sm focus:ring-primary/20"
                            name="remember">
                        <label for="remember_me" class="ml-2 text-sm text-slate-600 dark:text-slate-400">Lembrar de
                            mim</label>
                    </div>

                    <button type="submit"
                        class="w-full bg-primary hover:bg-emerald-600 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-primary/25 transition-all active:scale-[0.98] mt-2">
                        Entrar
                    </button>

                    @if(false) {{-- Ocultado a pedido do usuário --}}
                        <div class="relative py-2">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-slate-100 dark:border-slate-800"></div>
                            </div>
                            <div class="relative flex justify-center text-[10px] uppercase font-bold tracking-wider">
                                <span class="bg-white dark:bg-slate-950 px-4 text-slate-400">OU CONTINUE COM</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <button type="button"
                                class="flex items-center justify-center gap-2 px-4 py-2.5 border border-slate-200 dark:border-slate-800 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-900 transition-colors font-semibold text-sm text-slate-700 dark:text-slate-300">
                                <svg class="w-4 h-4" viewBox="0 0 24 24">
                                    <path
                                        d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                                        fill="#4285F4"></path>
                                    <path
                                        d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-1.01.68-2.31 1.09-3.71 1.09-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                                        fill="#34A853"></path>
                                    <path
                                        d="M5.84 14.13c-.22-.68-.35-1.4-.35-2.13s.13-1.45.35-2.13V7.01H2.18C1.43 8.51 1 10.21 1 12s.43 3.49 1.18 4.99l3.66-2.86z"
                                        fill="#FBBC05"></path>
                                    <path
                                        d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.01l3.66 2.86c.87-2.6 3.3-4.53 6.16-4.53z"
                                        fill="#EA4335"></path>
                                </svg>
                                Google
                            </button>
                            <button type="button"
                                class="flex items-center justify-center gap-2 px-4 py-2.5 border border-slate-200 dark:border-slate-800 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-900 transition-colors font-semibold text-sm text-slate-700 dark:text-slate-300">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                    <path
                                        d="M17.05 20.28c-.98.95-2.05.88-3.08.4-1.09-.5-2.08-.48-3.24 0-1.44.62-2.2.44-3.06-.4C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.75 1.18-.02 2.31-.93 3.81-.84 1.53.09 2.69.74 3.42 1.84-3.15 1.88-2.64 6.2.24 7.35-.61 1.53-1.41 3.03-2.55 3.87zm-4.72-13.49c-.04-1.61 1.34-3.11 2.91-3.23.14 1.77-1.31 3.25-2.91 3.23z">
                                    </path>
                                </svg>
                                Apple
                            </button>
                        </div>
                    @endif
                </form>
                <p class="mt-6 lg:mt-8 text-center text-sm text-slate-500 dark:text-slate-400">
                    Não tem uma conta?
                    <a class="font-bold text-slate-900 dark:text-white hover:text-primary transition-colors"
                        href="{{ route('register') }}">Criar uma conta</a>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>