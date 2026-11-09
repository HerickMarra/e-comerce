@if($products->isEmpty())
    <div class="bg-white dark:bg-slate-900 rounded-3xl p-12 text-center border border-slate-100 dark:border-slate-800">
        <div class="w-20 h-20 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-6">
            <span class="material-symbols-outlined text-4xl text-slate-300">search_off</span>
        </div>
        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Nenhum produto encontrado</h3>
        <p class="text-slate-500 max-w-sm mx-auto">Tente ajustar seus filtros ou buscar por outro termo.</p>
        <a href="{{ route('search') }}"
            class="inline-block mt-8 bg-primary text-white font-bold py-3 px-8 rounded-xl hover:shadow-lg hover:shadow-primary/20 transition-all">Ver
            todos os produtos</a>
    </div>
@else
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
        @foreach($products as $product)
            <div @class([
                'group relative bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 overflow-hidden hover:shadow-xl hover:shadow-slate-200/40 dark:hover:shadow-none transition-all duration-500 flex flex-col',
                'opacity-60' => $product->stock <= 0
            ])>
                <div class="aspect-[3/4] overflow-hidden relative">
                    @php 
                        $imagePath = $product->images->where('is_main', true)->first()?->path ?? ($product->images->first()?->path ?? '');
                    @endphp
                    <img src="{{ str_starts_with($imagePath, 'http') ? $imagePath : asset('storage/' . $imagePath) }}"
                        alt="{{ $product->name }}"
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <button
                        class="absolute top-2 right-2 sm:top-4 sm:right-4 w-8 h-8 sm:w-10 sm:h-10 bg-white/80 backdrop-blur-md rounded-full flex items-center justify-center text-slate-400 hover:text-red-500 transition-colors">
                        <span class="material-symbols-outlined text-base sm:text-xl">favorite</span>
                    </button>
                    <div
                        class="absolute inset-x-0 bottom-0 p-2 sm:p-4 translate-y-full group-hover:translate-y-0 transition-transform duration-300 bg-gradient-to-t from-black/60 to-transparent">
                        <a href="{{ route('product.show', $product->slug) }}"
                            class="w-full bg-white text-slate-900 font-bold py-2 sm:py-3 rounded-xl flex items-center justify-center gap-1 sm:gap-2 hover:bg-primary hover:text-white transition-all text-xs sm:text-sm shadow-lg">
                            <span class="material-symbols-outlined text-sm sm:text-lg">visibility</span>
                            Visualizar
                        </a>
                    </div>
                    @if($product->hasDiscount())
                        <div class="absolute top-2 left-2 sm:top-4 sm:left-4 bg-emerald-500 text-white text-[9px] sm:text-[10px] font-black px-2 py-0.5 sm:py-1 rounded-full uppercase tracking-wider z-10 shadow-md">
                            -{{ $product->discountPercentage() }}%
                        </div>
                    @endif
                    @if($product->stock <= 0)
                        <div
                            class="absolute top-2 right-2 sm:top-4 sm:right-4 bg-red-500 text-white text-[9px] sm:text-[10px] font-bold px-2 py-0.5 sm:py-1 rounded-full uppercase tracking-widest z-10">
                            Esgotado
                        </div>
                    @endif
                </div>
                <div class="p-3 sm:p-5 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-1 mb-1">
                            @if($product->brand)
                                <span class="text-[10px] font-black uppercase text-primary tracking-wider">{{ $product->brand }}</span>
                            @endif
                            @if($product->gender)
                                <span class="text-[9px] font-bold text-slate-400 uppercase">• {{ $product->gender }}</span>
                            @endif
                        </div>
                        <h3
                            class="font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors line-clamp-1 mb-1 text-xs sm:text-base">
                            {{ $product->name }}
                        </h3>

                        {{-- Cores do Tênis: Se houver filtro de cor ativo, exibe estritamente as cores do filtro que o produto possui --}}
                        @php
                            $matchingColors = !empty($selectedColors)
                                ? $product->colors->whereIn('hex_code', $selectedColors)
                                : $product->colors;
                        @endphp
                        @if($matchingColors->count() > 0)
                            <div class="flex items-center gap-1.5 mb-1.5 flex-wrap">
                                @foreach($matchingColors as $color)
                                    <span class="size-3 sm:size-3.5 rounded-full border border-slate-200 dark:border-slate-700 shadow-sm inline-block shrink-0"
                                          style="background-color: {{ $color->hex_code }}"
                                          title="{{ $color->color_name }}"></span>
                                @endforeach
                                @if(empty($selectedColors) && $product->colors->count() > $matchingColors->count())
                                    <span class="text-[9px] text-slate-400 font-bold">+{{ $product->colors->count() - $matchingColors->count() }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-50 dark:border-slate-800">
                        <div>
                            @if($product->hasDiscount())
                                <span class="text-[10px] text-slate-400 line-through block -mb-0.5">
                                    R$ {{ number_format($product->compare_price, 2, ',', '.') }}
                                </span>
                            @endif
                            <span class="text-xs sm:text-lg font-black text-slate-900 dark:text-white">R$
                                {{ number_format($product->price, 2, ',', '.') }}</span>
                        </div>
                        <form action="{{ route('cart.add', $product) }}" method="POST">
                            @csrf
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" @if($product->stock <= 0) disabled @endif
                                class="w-7 h-7 sm:w-10 sm:h-10 bg-slate-50 dark:bg-slate-800 rounded-lg flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                <span class="material-symbols-outlined text-sm sm:text-xl">add_shopping_cart</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-16 flex justify-center">
        {{ $products->links() }}
    </div>
@endif
