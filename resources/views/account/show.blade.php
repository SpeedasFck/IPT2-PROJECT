@extends('layouts.storefront')

@section('title', 'Your account')

@section('content')
<section class="page-intro">
    <div class="container-xl">
        <span class="eyebrow">YOUR CAREWELL ACCOUNT</span>
        <h1>Hello, {{ auth()->user()->name }}</h1>
        <p>Manage your account and review your orders.</p>
    </div>
</section>
<section class="section-pad">
    <div class="container-xl account-summary">
        @if (session('status'))<div class="status-alert" role="status">{{ session('status') }}</div>@endif
        <div class="account-summary-row"><div><span class="eyebrow">ACCOUNT EMAIL</span><p>{{ auth()->user()->email }}</p></div><a class="text-link" href="{{ route('account.edit') }}">Edit details <i class="bi bi-pencil" aria-hidden="true"></i></a></div>
        <div class="account-summary-row"><div><span class="eyebrow">DELIVERY CONTACT</span><p>{{ auth()->user()->phone ?: 'No phone number saved' }}<br>{{ auth()->user()->delivery_address ?: 'No delivery address saved' }}</p></div><a class="text-link" href="{{ route('account.edit') }}">Manage <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a></div>
        <div class="account-summary-row"><div><span class="eyebrow">ORDERS</span><p>{{ $orders->total() }} {{ Str::plural('order', $orders->total()) }}</p></div></div>
        @forelse ($orders as $order)
            <article class="account-order">
                <div><span class="eyebrow">{{ $order->order_number }}</span><p>{{ $order->created_at->format('M j, Y') }} · {{ ucfirst($order->status) }}</p></div>
                <div class="account-order-total"><strong>₱{{ number_format((float) $order->total, 2) }}</strong><a href="{{ route('orders.confirmation', $order->confirmation_token) }}">Order details</a></div>
            </article>
        @empty
            <p class="account-empty-orders">Your order history will appear here.</p>
        @endforelse
        {{ $orders->links() }}
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="button-outline" type="submit">Sign out <i class="bi bi-box-arrow-right" aria-hidden="true"></i></button></form>
    </div>
</section>
@endsection