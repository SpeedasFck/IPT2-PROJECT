<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') | Carewell Admin</title>
    @vite(['resources/css/storefront.css', 'resources/js/storefront.js'])
</head>
<body class="admin-body">
    <header class="admin-header"><a class="brand-lockup" href="{{ route('admin.dashboard') }}"><span class="brand-mark"><i class="bi bi-plus-lg" aria-hidden="true"></i></span><span class="brand-words">care<span>well</span><small>SHOP ADMIN</small></span></a><nav class="admin-nav" aria-label="Admin navigation"><a href="{{ route('admin.dashboard') }}">Overview</a><a href="{{ route('admin.products.index') }}">Products</a><a href="{{ route('admin.categories.index') }}">Categories</a><a href="{{ route('admin.orders.index') }}">Orders</a><a href="{{ route('admin.customers.index') }}">Customers</a><a href="{{ route('admin.reports.index') }}">Reports</a></nav><div class="admin-user">{{ auth()->user()->name }}<form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" aria-label="Sign out"><i class="bi bi-box-arrow-right" aria-hidden="true"></i></button></form></div></header>
    <main class="admin-main">@if (session('status'))<div class="status-alert" role="status">{{ session('status') }}</div>@endif @yield('content')</main>
</body>
</html>