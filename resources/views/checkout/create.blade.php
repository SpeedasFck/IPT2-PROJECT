@extends('layouts.storefront')

@section('title', 'Checkout')

@section('content')
<section class="page-intro"><div class="container-xl"><span class="eyebrow">SECURE ORDER DETAILS</span><h1>Checkout</h1><p>We only ask for details needed to deliver your order.</p></div></section>
<section class="section-pad"><div class="container-xl checkout-layout">
    <form method="POST" action="{{ route('checkout.store') }}" class="checkout-form">
        @csrf
        @if ($errors->any())<div class="form-alert" role="alert">Please check your details. {{ $errors->first() }}</div>@endif
        <div class="checkout-section-heading"><span class="eyebrow">01 / DELIVERY DETAILS</span><h2>Where should we deliver?</h2></div>
        <div class="checkout-fields">
            <label>Full name<input type="text" name="customer_name" value="{{ old('customer_name', auth()->user()?->name) }}" autocomplete="name" maxlength="120" required>@error('customer_name')<small class="field-error">{{ $message }}</small>@enderror</label>
            <label>Email address<input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" autocomplete="email" required>@error('email')<small class="field-error">{{ $message }}</small>@enderror</label>
            <label>Philippine mobile number<input type="tel" name="phone" value="{{ old('phone', auth()->user()?->phone) }}" placeholder="09XXXXXXXXX" autocomplete="tel" required>@error('phone')<small class="field-error">Use a mobile number such as 09XXXXXXXXX or +639XXXXXXXXX.</small>@enderror</label>
            <label class="field-wide">Street, building, barangay, and other delivery details<textarea name="delivery_address" rows="3" maxlength="1000" autocomplete="street-address" required>{{ old('delivery_address', auth()->user()?->delivery_address) }}</textarea>@error('delivery_address')<small class="field-error">{{ $message }}</small>@enderror</label>
            <label>City or municipality<input type="text" name="city" value="{{ old('city', auth()->user()?->city) }}" autocomplete="address-level2" maxlength="100" required>@error('city')<small class="field-error">{{ $message }}</small>@enderror</label>
            <label>Province<input type="text" name="province" value="{{ old('province', auth()->user()?->province) }}" autocomplete="address-level1" maxlength="100" required>@error('province')<small class="field-error">{{ $message }}</small>@enderror</label>
            <label>Postal code<input type="text" name="postal_code" value="{{ old('postal_code', auth()->user()?->postal_code) }}" inputmode="numeric" autocomplete="postal-code" pattern="[0-9]{4}" maxlength="4" required>@error('postal_code')<small class="field-error">Enter a 4-digit Philippine postal code.</small>@enderror</label>
        </div>
        <div class="checkout-section-heading payment-heading"><span class="eyebrow">02 / PAYMENT</span><h2>Payment method</h2></div>
        <label class="payment-option"><input type="radio" name="payment_method" value="cod" checked><span><strong>Cash on Delivery</strong><small>Pay when your order arrives.</small></span><i class="bi bi-cash-coin" aria-hidden="true"></i></label>
        <button class="button-primary checkout-submit" type="submit">Place COD order <i class="bi bi-arrow-right" aria-hidden="true"></i></button>
    </form>
    <aside class="checkout-summary"><span class="eyebrow">ORDER SUMMARY</span>@foreach ($items as $item)<div class="checkout-item"><span>{{ $item['product']->name }} <small>× {{ $item['quantity'] }}</small></span><strong>₱{{ number_format((float) $item['product']->price * $item['quantity'], 2) }}</strong></div>@endforeach<div class="summary-row"><span>Subtotal</span><strong>₱{{ number_format($subtotal, 2) }}</strong></div><div class="summary-row"><span>Delivery fee</span><strong>{{ $deliveryFee > 0 ? '₱' . number_format($deliveryFee, 2) : 'To be confirmed' }}</strong></div><div class="summary-total"><span>Total</span><strong>₱{{ number_format($subtotal + $deliveryFee, 2) }}{{ $deliveryFee == 0 ? '+' : '' }}</strong></div><p class="delivery-config-note">{{ $deliveryFee == 0 ? 'Delivery fee has not been configured yet and is not included in this estimate.' : 'Delivery fee is configurable by the shop.' }}</p></aside>
</div></section>
@endsection