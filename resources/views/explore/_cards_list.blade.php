@foreach($products as $product)
    @include('explore._card', ['product' => $product])
@endforeach
