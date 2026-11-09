<div class="flex flex-wrap gap-2">
    @forelse($availableColors as $color)
        <label class="relative cursor-pointer group" title="{{ $color->color_name }}">
            <input type="checkbox" name="colors[]" value="{{ $color->hex_code }}"
                {{ in_array($color->hex_code, $selectedColors) ? 'checked' : '' }}
                @change="updateFilters()"
                class="sr-only peer">
            <div class="w-8 h-8 rounded-full border-2 border-slate-200 dark:border-slate-700 peer-checked:border-primary peer-checked:ring-2 peer-checked:ring-primary/40 peer-checked:ring-offset-2 peer-checked:scale-110 transition-all hover:scale-105 shadow-sm"
                 style="background-color: {{ $color->hex_code }}"></div>
            <span class="sr-only">{{ $color->color_name }}</span>
        </label>
    @empty
        <span class="text-xs text-slate-400 italic">Nenhuma cor para o filtro selecionado.</span>
    @endforelse
</div>

