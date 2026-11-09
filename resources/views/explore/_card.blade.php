@php
    /** @var \App\Models\Product $product */
    $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->first();
    $imagePath = $mainImage?->path ?? ($product->image ?? '');
    $imageUrl = '';
    if ($imagePath) {
        $imageUrl = str_starts_with($imagePath, 'http') ? $imagePath : asset('storage/' . $imagePath);
    }
    $isOutOfStock = $product->stock <= 0;
    $hasDiscount = $product->hasDiscount();
@endphp

<article @class([
    'group relative bg-white dark:bg-slate-900 rounded-2xl sm:rounded-3xl border border-slate-100 dark:border-slate-800 overflow-hidden hover:shadow-xl hover:shadow-slate-200/50 dark:hover:shadow-none transition-all duration-500 flex flex-col',
    'opacity-65 grayscale-[30%]' => $isOutOfStock,
]) data-product-id="{{ $product->id }}">
    {{-- Imagem e Badges --}}
    <div class="aspect-[3/4] overflow-hidden relative bg-slate-50 dark:bg-slate-800">
        @if($imageUrl)
            <img src="{{ $imageUrl }}"
                alt="Tênis {{ $product->name }}{{ $product->brand ? ' - ' . $product->brand : '' }}"
                loading="lazy"
                decoding="async"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out">
        @else
            <div class="w-full h-full flex flex-col items-center justify-center text-slate-300 dark:text-slate-600 p-4 text-center">
                <span class="material-symbols-outlined text-4xl sm:text-5xl mb-1">steps</span>
                <span class="text-[10px] sm:text-xs font-semibold">Sem foto</span>
            </div>
        @endif

        {{-- Link absoluto transparente sobre a foto --}}
        <a href="{{ route('product.show', $product->slug) }}" class="absolute inset-0 z-10" aria-label="Ver {{ $product->name }}"></a>

        {{-- Badges de Desconto & Esgotado --}}
        <div class="absolute top-2 left-2 sm:top-3 sm:left-3 flex flex-col gap-1 z-20 pointer-events-none">
            @if($hasDiscount)
                <span class="bg-emerald-500 text-white text-[9px] sm:text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider shadow-md shadow-emerald-500/20">
                    -{{ $product->discountPercentage() }}%
                </span>
            @endif
        </div>

        @if($isOutOfStock)
            <div class="absolute top-2 right-2 sm:top-3 sm:right-3 bg-red-500 text-white text-[9px] sm:text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-widest z-20 shadow-md shadow-red-500/20">
                Esgotado
            </div>
        @endif

        {{-- Ação rápida de Visualizar (Hover Desktop) --}}
        <div class="absolute inset-x-0 bottom-0 p-2 sm:p-3 translate-y-full group-hover:translate-y-0 transition-transform duration-300 bg-gradient-to-t from-black/60 to-transparent hidden sm:block z-20">
            <a href="{{ route('product.show', $product->slug) }}"
                class="w-full bg-white text-slate-900 font-bold py-2 rounded-xl flex items-center justify-center gap-1.5 hover:bg-primary hover:text-white transition-all text-xs shadow-lg">
                <span class="material-symbols-outlined text-base">visibility</span>
                Ver Detalhes
            </a>
        </div>
    </div>

    {{-- Conteúdo do Card --}}
    <div class="p-2.5 sm:p-4 flex-1 flex flex-col justify-between gap-2">
        <div>
            {{-- Marca e Gênero --}}
            <div class="flex items-center gap-1 mb-1 flex-wrap">
                @if($product->brand)
                    <span class="text-[9px] sm:text-[10px] font-black uppercase text-primary tracking-wider">{{ $product->brand }}</span>
                @endif
                @if($product->gender)
                    <span class="text-[9px] font-bold text-slate-400 uppercase">• {{ $product->gender }}</span>
                @endif
            </div>

            {{-- Nome do Tênis --}}
            <h3 class="font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors line-clamp-2 text-xs sm:text-sm leading-snug min-h-[2rem]">
                <a href="{{ route('product.show', $product->slug) }}" class="hover:underline">
                    {{ $product->name }}
                </a>
            </h3>

            {{-- Amostras de Cores (se houver) --}}
            @if($product->colors && $product->colors->count() > 0)
                <div class="flex items-center gap-1 mt-1.5 flex-wrap">
                    @foreach($product->colors->take(4) as $color)
                        <span class="size-2.5 sm:size-3 rounded-full border border-slate-200 dark:border-slate-700 shadow-xs inline-block shrink-0"
                              style="background-color: {{ $color->hex_code }}"
                              title="{{ $color->color_name }}"></span>
                    @endforeach
                    @if($product->colors->count() > 4)
                        <span class="text-[8px] sm:text-[9px] text-slate-400 font-bold">+{{ $product->colors->count() - 4 }}</span>
                    @endif
                </div>
            @endif
        </div>

        {{-- Preço e Botão de Ação --}}
        <div class="pt-2 border-t border-slate-100 dark:border-slate-800/80 flex items-end justify-between gap-1">
            <div class="min-w-0">
                @if($hasDiscount)
                    <span class="text-[9px] sm:text-[10px] text-slate-400 line-through block -mb-0.5 truncate">
                        R$ {{ number_format($product->compare_price, 2, ',', '.') }}
                    </span>
                @endif
                <span class="text-xs sm:text-base font-black text-slate-900 dark:text-white truncate block">
                    R$ {{ number_format($product->price, 2, ',', '.') }}
                </span>
            </div>

            <a href="{{ route('product.show', $product->slug) }}"
                aria-label="Comprar {{ $product->name }}"
                class="w-7 h-7 sm:w-9 sm:h-9 bg-primary/10 hover:bg-primary text-primary hover:text-white rounded-lg sm:rounded-xl flex items-center justify-center transition-all shrink-0">
                <span class="material-symbols-outlined text-sm sm:text-base">shopping_bag</span>
            </a>
        </div>
    </div>
</article>

