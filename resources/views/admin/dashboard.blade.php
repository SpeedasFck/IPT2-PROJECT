@extends('admin.layout')

@section('title', 'Overview')

@section('content')
<div class="admin-page-heading"><div><span class="eyebrow">SHOP MANAGEMENT</span><h1>Overview</h1></div><a class="button-primary" href="{{ route('admin.products.create') }}"><i class="bi bi-plus-lg" aria-hidden="true"></i> Add product</a></div>
<div class="admin-stat-grid">
    <div class="admin-stat"><span>Products</span><strong>{{ $productCount }}</strong><i class="bi bi-box-seam" aria-hidden="true"></i></div>
    <div class="admin-stat"><span>Orders</span><strong>{{ $orderCount }}</strong><i class="bi bi-bag-check" aria-hidden="true"></i></div>
    <div class="admin-stat"><span>Pending orders</span><strong>{{ $pendingCount }}</strong><i class="bi bi-hourglass-split" aria-hidden="true"></i></div>
    <div class="admin-stat"><span>Delivered</span><strong>{{ $completedCount }}</strong><i class="bi bi-check2-circle" aria-hidden="true"></i></div>
    <div class="admin-stat"><span>Customers</span><strong>{{ $customerCount }}</strong><i class="bi bi-people" aria-hidden="true"></i></div>
    <div class="admin-stat {{ $lowStockCount ? 'admin-stat-alert' : '' }}"><span>Low stock</span><strong>{{ $lowStockCount }}</strong><i class="bi bi-exclamation-triangle" aria-hidden="true"></i></div>
</div>
<section class="admin-panel"><div class="admin-panel-heading"><div><span class="eyebrow">CONFIRMED AND FULFILLED</span><h2>Sales summary</h2></div><strong class="admin-sales-total">₱{{ number_format((float) $salesTotal, 2) }}</strong></div><p>Based on orders in confirmed, processing, shipped, or delivered status.</p></section>
<section class="admin-panel"><div class="admin-panel-heading"><div><span class="eyebrow">LATEST ACTIVITY</span><h2>Recent orders</h2></div><a class="text-link" href="{{ route('admin.orders.index') }}">All orders <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a></div>
    <div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Order</th><th>Customer</th><th>Date</th><th>Status</th><th>Total</th></tr></thead><tbody>@forelse ($recentOrders as $order)<tr><td><a href="{{ route('admin.orders.show', $order) }}">{{ $order->order_number }}</a></td><td>{{ $order->customer_name }}</td><td>{{ $order->created_at->format('M j, Y') }}</td><td><span class="status-pill status-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td><td>₱{{ number_format((float) $order->total, 2) }}</td></tr>@empty<tr><td colspan="5">No orders recorded yet.</td></tr>@endforelse</tbody></table></div>
</section>
@endsection