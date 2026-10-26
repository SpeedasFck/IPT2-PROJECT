@extends('admin.layout')

@section('title', 'Products')

@section('content')
<div class="admin-page-heading"><div><span class="eyebrow">CATALOGUE</span><h1>Products</h1></div><a class="button-primary" href="{{ route('admin.products.create') }}"><i class="bi bi-plus-lg" aria-hidden="true"></i> Add product</a></div>
<section class="admin-panel"><div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Visibility</th><th></th></tr></thead><tbody>@forelse ($products as $product)<tr><td><strong>{{ $product->name }}</strong><small>{{ $product->sku ?: 'No SKU' }}</small></td><td>{{ $product->category->name }}</td><td>₱{{ number_format((float) $product->price, 2) }}</td><td><span class="{{ $product->stock <= $product->low_stock_threshold ? 'low-stock' : '' }}">{{ $product->stock }}</span></td><td>{{ $product->is_active ? 'Active' : 'Inactive' }}</td><td><a class="table-action" href="{{ route('admin.products.edit', $product) }}">Edit</a></td></tr>@empty<tr><td colspan="6">No products yet.</td></tr>@endforelse</tbody></table></div>{{ $products->links() }}</section>
@endsection