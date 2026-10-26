@extends('layouts.storefront')

@section('title', 'Shop healthcare essentials')

@section('content')
    <section class="page-intro">
        <div class="container-xl">
            <span class="eyebrow">THE CAREWELL SHOP</span>
            <h1>Everyday essentials</h1>
            <p>Browse the current selection of healthcare and personal-care basics.</p>
        </div>
    </section>
    <section class="shop-section section-pad">
        <div class="container-xl">
            <form class="shop-filters" method="GET" action="{{ route('shop.index') }}">
                <label class="search-field"><span class="visually-hidden">Search products</span><i class="bi bi-search" aria-hidden="true"></i><input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search products" maxlength="100"></label>
                <label><span class="visually-hidden">Category</span><select name="category"><option value="">All categories</option>@foreach ($categories as $category)<option value="{{ $category->slug }}" @selected(($filters['category'] ?? '') === $category->slug)>{{ $category->name }}</option>@endforeach</select></label>
                <label><span class="visually-hidden">Minimum price</span><input type="number" name="min_price" min="0" step="0.01" value="{{ $filters['min_price'] ?? '' }}" placeholder="Min ₱"></label>
                <label><span class="visually-hidden">Maximum price</span><input type="number" name="max_price" min="0" step="0.01" value="{{ $filters['max_price'] ?? '' }}" placeholder="Max ₱"></label>
                <label><span class="visually-hidden">Sort products</span><select name="sort"><option value="newest" @selected(($filters['sort'] ?? 'newest') === 'newest')>Newest</option><option value="name" @selected(($filters['sort'] ?? '') === 'name')>Name</option><option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>Price: low to high</option><option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>Price: high to low</option></select></label>
                <button class="button-primary filter-submit" type="submit">Apply filters <i class="bi bi-sliders" aria-hidden="true"></i></button>
                @if (count($filters))<a class="filter-reset" href="{{ route('shop.index') }}">Clear</a>@endif
            </form>
            <div class="shop-results-heading"><p>{{ $products->total() }} {{ Str::plural('product', $products->total()) }}</p></div>
            <div class="row g-4 product-grid">
                @forelse ($products as $product)
                    <div class="col-6 col-lg-3">
                        <article class="product-card h-100">
                            <a class="product-image" href="{{ route('products.show', $product) }}" aria-label="View {{ $product->name }}">
                                <img src="{{ $product->image_src ?: 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=900&q=85' }}" alt="Illustrative image for {{ $product->name }}" loading="lazy">
                                <span class="stock-label"><span></span> In stock</span><span class="product-view"><i class="bi bi-arrow-up-right" aria-hidden="true"></i></span>
                            </a>
                            <div class="product-info"><span class="product-category">{{ $product->category->name }}</span><h2><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></h2><div class="product-meta"><span class="product-price">₱{{ number_format((float) $product->price, 2) }}</span><span class="product-small-stock">Available</span></div></div>
                        </article>
                    </div>
                @empty
                    <div class="col-12"><div class="empty-catalog"><span class="empty-catalog-icon"><i class="bi bi-search" aria-hidden="true"></i></span><h2>No matching products</h2><p>Try changing your search or filters to see more essentials.</p><a class="button-outline" href="{{ route('shop.index') }}">Clear filters</a></div></div>
                @endforelse
            </div>
            <div class="pagination-wrap">{{ $products->links() }}</div>
        </div>
    </section>
@endsection