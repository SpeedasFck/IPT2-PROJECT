@extends('layouts.storefront')

@section('title', $product->name)

@section('content')
    <section class="product-detail-section section-pad">
        <div class="container-xl">
            <nav class="breadcrumb-nav" aria-label="Breadcrumb"><a href="{{ route('home') }}">Home</a><i class="bi bi-chevron-right" aria-hidden="true"></i><a href="{{ route('shop.index') }}">Shop</a><i class="bi bi-chevron-right" aria-hidden="true"></i><span>{{ $product->name }}</span></nav>
            <div class="product-detail-grid">
                <div class="detail-image"><img src="{{ $product->image_src ?: 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=1200&q=85' }}" alt="Illustrative image for {{ $product->name }}"></div>
                <div class="detail-copy">
                    <a class="product-category detail-category" href="{{ route('shop.index', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a>
                    <h1>{{ $product->name }}</h1>
                    <p class="detail-price">₱{{ number_format((float) $product->price, 2) }}</p>
                    <p class="detail-description">{{ $product->description }}</p>
                    <p class="detail-availability"><span></span> In stock</p>
                    <div class="manufacturer-note"><i class="bi bi-info-circle" aria-hidden="true"></i><p>Review the manufacturer's label and instructions before use. Product information here is not medical advice.</p></div>
                    <form method="POST" action="{{ route('cart.add', $product) }}" class="add-to-cart-form">
                        @csrf
                        <label for="quantity">Quantity</label>
                        <input id="quantity" type="number" name="quantity" value="1" min="1" max="{{ min(99, $product->stock) }}" required>
                        <button class="button-primary" type="submit">Add to cart <i class="bi bi-bag-plus" aria-hidden="true"></i></button>
                    </form>
                    @if ($errors->has('quantity') || $errors->has('cart'))<p class="form-alert" role="alert">{{ $errors->first() }}</p>@endif
                    <a class="button-outline" href="{{ route('shop.index') }}">Back to shop <i class="bi bi-arrow-left" aria-hidden="true"></i></a>
                </div>
            </div>
            @if ($relatedProducts->isNotEmpty())
                <div class="related-heading"><span class="eyebrow">MORE FROM {{ strtoupper($product->category->name) }}</span><h2>Related essentials</h2></div>
                <div class="row g-4 product-grid">
                    @foreach ($relatedProducts as $relatedProduct)
                        <div class="col-6 col-lg-3"><article class="product-card h-100"><a class="product-image" href="{{ route('products.show', $relatedProduct) }}"><img src="{{ $relatedProduct->image_src ?: 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=900&q=85' }}" alt="Illustrative image for {{ $relatedProduct->name }}" loading="lazy"><span class="stock-label"><span></span> In stock</span></a><div class="product-info"><span class="product-category">{{ $relatedProduct->category->name }}</span><h3><a href="{{ route('products.show', $relatedProduct) }}">{{ $relatedProduct->name }}</a></h3><div class="product-meta"><span class="product-price">₱{{ number_format((float) $relatedProduct->price, 2) }}</span><span class="product-small-stock">Available</span></div></div></article></div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection