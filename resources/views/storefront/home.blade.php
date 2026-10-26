@extends('layouts.storefront')

@section('title', 'Philippine medical essentials')

@section('content')
    <section class="hero-section">
        <div class="container-xl hero-grid">
            <div class="hero-copy">
                <span class="eyebrow"><span class="eyebrow-line"></span> QUALITY CARE FOR EVERYDAY LIFE</span>
                <h1>Philippine medical essentials, <em>ready for home and family care.</em></h1>
                <p>From first-aid basics and hygiene supplies to vital-care tools, find dependable products made for daily Filipino living.</p>
                <div class="hero-actions">
                    <a class="button-primary" href="{{ route('shop.index') }}">Shop medical essentials <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                    <a class="text-link" href="#categories">Explore categories <i class="bi bi-arrow-down-right" aria-hidden="true"></i></a>
                </div>
                <div class="hero-note"><span class="note-icon"><i class="bi bi-check2" aria-hidden="true"></i></span><span>Trusted essentials for everyday health and safety</span></div>
            </div>
            <div class="hero-visual">
                <img src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=1400&q=90" alt="Illustrative healthcare setting with a professional preparing everyday supplies" fetchpriority="high">
                <div class="hero-stamp"><i class="bi bi-plus-lg" aria-hidden="true"></i><span>CARE FOR<br>EVERYDAY</span></div>
                <div class="hero-caption"><span>01 / 03</span><span>Essentials for your home</span></div>
            </div>
        </div>
        <div class="hero-bottom-rule container-xl"><span>Care, made a little simpler</span><span><i class="bi bi-arrow-down" aria-hidden="true"></i></span></div>
    </section>

    <section class="category-section section-pad" id="categories" aria-labelledby="categories-heading">
        <div class="container-xl">
            <div class="section-heading">
                <div><span class="eyebrow">FIND WHAT YOU NEED</span><h2 id="categories-heading">Browse by category</h2></div>
                <a class="text-link" href="{{ route('shop.index') }}">View all medical items <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
            </div>
            <div class="category-grid">
                @forelse ($categories as $category)
                    <a class="category-item" href="{{ route('shop.index', ['category' => $category->slug]) }}">
                        <span class="category-icon"><i class="bi {{ $category->icon ?: 'bi-plus-circle' }}" aria-hidden="true"></i></span>
                        <span class="category-name">{{ $category->name }}</span>
                        <span class="category-count">{{ $category->available_products_count }} {{ Str::plural('item', $category->available_products_count) }}</span>
                        <i class="bi bi-arrow-up-right category-arrow" aria-hidden="true"></i>
                    </a>
                @empty
                    <p class="empty-state">Categories are being prepared. Please check back soon.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="products-section section-pad" aria-labelledby="products-heading">
        <div class="container-xl">
            <div class="section-heading">
                <div><span class="eyebrow">A GOOD PLACE TO START</span><h2 id="products-heading">Philippine medical essentials</h2></div>
                <a class="text-link" href="{{ route('shop.index') }}">Shop all products <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
            </div>
            <div class="row g-4 product-grid">
                @forelse ($products as $product)
                    <div class="col-6 col-lg-3">
                        <article class="product-card h-100">
                            <a class="product-image" href="{{ route('products.show', $product) }}" aria-label="View {{ $product->name }}">
                                <img src="{{ $product->image_src ?: 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=900&q=85' }}" alt="Illustrative image for {{ $product->name }}" loading="lazy">
                                <span class="stock-label"><span></span> In stock</span>
                                <span class="product-view"><i class="bi bi-arrow-up-right" aria-hidden="true"></i></span>
                            </a>
                            <div class="product-info">
                                <span class="product-category">{{ $product->category->name }}</span>
                                <h3><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></h3>
                                <div class="product-meta"><span class="product-price">₱{{ number_format((float) $product->price, 2) }}</span><span class="product-small-stock">Available</span></div>
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12"><p class="empty-state">New products are on their way. Browse categories or visit again soon.</p></div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="care-section" id="about" aria-labelledby="care-heading">
        <div class="container-xl care-grid">
            <div class="care-photo"><img src="https://images.unsplash.com/photo-1579684385127-1ef15d508118?auto=format&fit=crop&w=1200&q=85" alt="Illustrative healthcare professional in a bright clinical setting" loading="lazy"></div>
            <div class="care-copy">
                <span class="eyebrow">A MORE CONSIDERED WAY TO SHOP</span>
                <h2 id="care-heading">Practical care for healthcare at home.</h2>
                <p>We curate dependable Philippine medical supplies that help families, clinics, and everyday households stay prepared with the basics they rely on most.</p>
                <a class="button-outline" href="{{ route('shop.index') }}">Explore the shop <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                <div class="care-values">
                    <div><i class="bi bi-box-seam" aria-hidden="true"></i><span>Practical everyday picks</span></div>
                    <div><i class="bi bi-card-checklist" aria-hidden="true"></i><span>Clear product details</span></div>
                    <div><i class="bi bi-house-heart" aria-hidden="true"></i><span>Made for home routines</span></div>
                </div>
            </div>
        </div>
    </section>

    <section class="closing-cta">
        <div class="container-xl closing-cta-inner">
            <div><span class="eyebrow">YOUR NEXT ESSENTIAL IS HERE</span><h2>Prepared for everyday health needs, one reliable item at a time.</h2></div>
            <a class="button-light" href="{{ route('shop.index') }}">Browse the shop <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
        </div>
    </section>
@endsection