<x-admin-layout>
    <div class="max-w-6xl mx-auto space-y-8">
        <!-- Header -->
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center">
                        <span class="material-symbols-outlined text-3xl">mail</span>
                    </div>
                    Inscrições Newsletter
                </h1>
                <p class="text-slate-500 mt-2 font-medium">Gerencie os e-mails capturados através do rodapé do site.</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="px-6 py-3 bg-slate-50 rounded-2xl border border-slate-100 text-sm font-bold text-slate-600">
                    Total: {{ $subscriptions->total() }} e-mails
                </div>
            </div>
        </div>

        <!-- Tabela -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-50">
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                E-mail</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Data
                                de Inscrição</th>
                            <th
                                class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">
                                Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($subscriptions as $subscription)
                            <tr class="group hover:bg-slate-50/50 transition-colors">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-primary/10 group-hover:text-primary transition-colors">
                                            <span class="material-symbols-outlined">alternate_email</span>
                                        </div>
                                        <span class="font-bold text-slate-700">{{ $subscription->email }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-bold text-slate-600">{{ $subscription->created_at->format('d/m/Y') }}</span>
                                        <span
                                            class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">{{ $subscription->created_at->format('H:i') }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <form action="{{ route('admin.newsletters.destroy', $subscription) }}" method="POST"
                                        onsubmit="return confirm('Tem certeza que deseja remover este e-mail?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-10 h-10 rounded-xl bg-white border border-slate-100 text-slate-400 hover:text-red-500 hover:bg-red-50 hover:border-red-100 transition-all flex items-center justify-center shadow-sm">
                                            <span class="material-symbols-outlined text-xl">delete</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-8 py-20 text-center">
                                    <div class="max-w-xs mx-auto space-y-4">
                                        <div
                                            class="w-20 h-20 rounded-3xl bg-slate-50 flex items-center justify-center mx-auto text-slate-200">
                                            <span class="material-symbols-outlined text-5xl">mail_lock</span>
                                        </div>
                                        <div>
                                            <p class="text-lg font-bold text-slate-900">Nenhuma inscrição encontrada</p>
                                            <p class="text-sm text-slate-500 font-medium">Os e-mails aparecerão aqui assim
                                                que as pessoas se inscreverem no rodapé.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($subscriptions->hasPages())
                <div class="px-8 py-6 bg-slate-50 border-t border-slate-100">
                    {{ $subscriptions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>