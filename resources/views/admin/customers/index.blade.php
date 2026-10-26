@extends('admin.layout')

@section('title', 'Customers')

@section('content')
<div class="admin-page-heading"><div><span class="eyebrow">CUSTOMER RECORDS</span><h1>Customers</h1></div></div>
<section class="admin-panel"><form class="admin-filter-form" method="GET" action="{{ route('admin.customers.index') }}"><label>Search name or email<input name="q" value="{{ $filters['q'] ?? '' }}" maxlength="100" placeholder="Search customers"></label><button class="button-primary" type="submit">Search</button><a class="text-link" href="{{ route('admin.customers.index') }}">Clear</a></form><div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Customer</th><th>Email</th><th>Orders</th><th>Joined</th></tr></thead><tbody>@forelse ($customers as $customer)<tr><td>{{ $customer->name }}</td><td>{{ $customer->email }}</td><td>{{ $customer->orders_count }}</td><td>{{ $customer->created_at->format('M j, Y') }}</td></tr>@empty<tr><td colspan="4">No customers found.</td></tr>@endforelse</tbody></table></div>{{ $customers->links() }}</section>
@endsection