@extends('layouts.storefront')

@section('title', 'Your cart')

@section('content')
<section class="page-intro"><div class="container-xl"><span class="eyebrow">YOUR SELECTION</span><h1>Shopping cart</h1><p>Review availability and quantities before checkout.</p></div></section>
<section class="section-pad"><div class="container-xl cart-layout">
    @if ($errors->has('cart'))<div class="form-alert" role="alert">{{ $errors->first('cart') }}</div>@endif
    @if (session('status'))<div class="status-alert" role="status">{{ session('status') }}</div>@endif
    @if ($items->isEmpty())
        <div class="empty-catalog"><span class="empty-catalog-icon"><i class="bi bi-bag" aria-hidden="true"></i></span><h2>Your cart is empty</h2><p>Explore the catalogue and add an available essential to get started.</p><a class="button-primary" href="{{ route('shop.index') }}">Browse the shop <i class="bi bi-arrow-right" aria-hidden="true"></i></a></div>
    @else
        <div class="cart-items">
            @foreach ($items as $item)
                <article class="cart-item">
                    <a class="cart-item-image" href="{{ route('products.show', $item['product']) }}"><img src="{{ $item['product']->image_src ?: 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=400&q=80' }}" alt="Illustrative image for {{ $item['product']->name }}"></a>
                    <div class="cart-item-details"><span class="product-category">{{ $item['product']->category->name }}</span><h2><a href="{{ route('products.show', $item['product']) }}">{{ $item['product']->name }}</a></h2><p>₱{{ number_format((float) $item['product']->price, 2) }} each · {{ $item['product']->stock > 0 ? $item['product']->stock . ' available' : 'Currently out of stock' }}</p></div>
                    <form method="POST" action="{{ route('cart.update', $item['product']) }}" class="cart-quantity-form">@csrf @method('PATCH')<label class="visually-hidden" for="quantity-{{ $item['product']->id }}">Quantity for {{ $item['product']->name }}</label><input id="quantity-{{ $item['product']->id }}" type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="{{ max(1, min(99, $item['product']->stock)) }}"><button type="submit" aria-label="Update {{ $item['product']->name }} quantity"><i class="bi bi-arrow-repeat" aria-hidden="true"></i></button></form>
                    <strong class="cart-line-total">₱{{ number_format((float) $item['product']->price * $item['quantity'], 2) }}</strong>
                    <form method="POST" action="{{ route('cart.remove', $item['product']) }}">@csrf @method('DELETE')<button class="icon-action" type="submit" aria-label="Remove {{ $item['product']->name }}"><i class="bi bi-trash3" aria-hidden="true"></i></button></form>
                </article>
            @endforeach
            <form method="POST" action="{{ route('cart.clear') }}" class="cart-clear">@csrf @method('DELETE')<button class="text-link" type="submit">Clear cart <i class="bi bi-trash3" aria-hidden="true"></i></button></form>
        </div>
        <aside class="cart-summary"><span class="eyebrow">ORDER SUMMARY</span><div class="summary-row"><span>Subtotal</span><strong>₱{{ number_format($subtotal, 2) }}</strong></div><div class="summary-row"><span>Delivery</span><span>Calculated at checkout</span></div>@if ($items->every(fn ($item) => $item['product']->stock >= $item['quantity']) && $items->every(fn ($item) => $item['product']->stock > 0))<a class="button-primary" href="{{ route('checkout.create') }}">Continue to checkout <i class="bi bi-arrow-right" aria-hidden="true"></i></a>@else<p class="field-error">Review unavailable items before checkout.</p>@endif<a class="summary-back" href="{{ route('shop.index') }}">Continue browsing</a></aside>
    @endif
</div></section>
@endsection