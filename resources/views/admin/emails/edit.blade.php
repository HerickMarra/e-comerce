<x-admin-layout>

    <head>
        <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
        <script>
            tinymce.init({
                selector: 'textarea[name="content"]',
                plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
                toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
                language: 'pt_BR',
                promotion: false,
                branding: false,
                height: 500,
            });
        </script>
    </head>
    <div class="max-w-4xl mx-auto space-y-8">
        <!-- Header -->
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.emails.index') }}"
                class="w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-all">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Editar Modelo</h1>
                <p class="text-slate-500 mt-1">Atualize o conteúdo do e-mail:
                    <strong>{{ $email->name }}</strong>
                </p>
            </div>
        </div>

        <form action="{{ route('admin.emails.update', $email) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Main Info -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 space-y-8">
                        <h2 class="text-xl font-bold text-slate-900 flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                                <span class="material-symbols-outlined">edit_note</span>
                            </div>
                            Conteúdo do E-mail
                        </h2>

                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Nome do Modelo
                                    (Interno)</label>
                                <input type="text" name="name" value="{{ old('name', $email->name) }}"
                                    class="w-full bg-slate-50 border-slate-200 rounded-2xl focus:ring-primary/50 focus:border-primary transition-all font-medium py-4 px-6 border-2"
                                    placeholder="Ex: Newsletter Semanal">
                                @error('name') <p class="mt-2 text-sm text-red-500 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Assunto do E-mail</label>
                                <input type="text" name="subject" value="{{ old('subject', $email->subject) }}"
                                    class="w-full bg-slate-50 border-slate-200 rounded-2xl focus:ring-primary/50 focus:border-primary transition-all font-medium py-4 px-6 border-2"
                                    placeholder="O que o cliente verá como assunto">
                                @error('subject') <p class="mt-2 text-sm text-red-500 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Corpo do E-mail
                                    (HTML)</label>
                                <textarea name="content" rows="12"
                                    class="w-full bg-slate-50 border-slate-200 rounded-2xl focus:ring-primary/50 focus:border-primary transition-all font-medium py-4 px-6 border-2">{{ old('content', $email->content) }}</textarea>
                                @error('content') <p class="mt-2 text-sm text-red-500 font-medium">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-xs text-slate-400">Variáveis disponíveis: {name}, {email},
                                    {order_number}, {status}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Info -->
                <div class="space-y-6">
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 space-y-6">
                        <h2 class="text-xl font-bold text-slate-900 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                <span class="material-symbols-outlined">category</span>
                            </div>
                            Status
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <p class="text-sm font-bold text-slate-700">Tipo:
                                    <span class="font-medium text-slate-500 capitalize">{{ $email->type }}</span>
                                </p>
                            </div>
                            @if($email->is_system)
                                <div class="p-4 bg-blue-50 rounded-2xl border border-blue-100">
                                    <p class="text-xs text-blue-700 font-medium">Este é um modelo de
                                        <strong>sistema</strong> e é essencial para o funcionamento automático. Não pode ser
                                        excluído.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="pt-4 flex flex-col gap-3">
                        <button type="submit"
                            class="w-full py-5 bg-primary hover:bg-emerald-700 text-white font-black text-lg rounded-3xl transition-all shadow-2xl shadow-primary/20 flex items-center justify-center gap-3">
                            <span class="material-symbols-outlined">save</span>
                            Atualizar Modelo
                        </button>
                        <a href="{{ route('admin.emails.index') }}"
                            class="w-full py-5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-3xl transition-all text-center">
                            Descartar
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-admin-layout>