<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f6faf7">
    <meta name="description" content="Philippine medical supplies and healthcare essentials for home, care, and daily wellbeing.">
    <title>@yield('title', 'PILIPINS MEDIKAL STOR') | {{ config('app.name', 'PILIPINS MEDIKAL STOR') }}</title>
    @vite(['resources/css/storefront.css', 'resources/js/storefront.js'])
</head>
<body>
    <a class="skip-link" href="#main-content">Skip to content</a>
    <div class="announcement-bar">
        <span>Philippine medical essentials</span>
        <span class="announcement-note">Care for every day <i class="bi bi-heart-pulse" aria-hidden="true"></i></span>
    </div>
    <header class="site-header">
        <nav class="navbar navbar-expand-lg container-xl" aria-label="Main navigation">
            <a class="navbar-brand brand-lockup" href="{{ route('home') }}" aria-label="PILIPINS MEDIKAL STOR home">
                <span class="brand-mark"><i class="bi bi-plus-lg" aria-hidden="true"></i></span>
                <span class="brand-words">PILIPINS <span>MEDIKAL</span><small>STOR</small></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mx-auto align-items-lg-center gap-lg-4">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('shop.*') ? 'active' : '' }}" href="{{ route('shop.index') }}">Shop</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#categories">Categories</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}#about">Our approach</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('contact.show') }}">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('cart.index') }}">Cart</a></li>
                </ul>
                @auth
                    <a class="header-shop-link" href="{{ auth()->user()->is_admin ? route('admin.dashboard') : route('account.show') }}">{{ auth()->user()->is_admin ? 'Admin' : 'My account' }} <i class="bi bi-person" aria-hidden="true"></i></a>
                @else
                    <a class="header-shop-link" href="{{ route('login') }}">Sign in <i class="bi bi-person" aria-hidden="true"></i></a>
                @endauth
            </div>
        </nav>
    </header>

    <main id="main-content">
        @yield('content')
    </main>

    <footer class="site-footer" id="footer">
        <div class="container-xl footer-main">
            <div class="footer-brand">
                <a class="brand-lockup brand-lockup-footer" href="{{ route('home') }}">
                    <span class="brand-mark"><i class="bi bi-plus-lg" aria-hidden="true"></i></span>
                    <span class="brand-words">PILIPINS <span>MEDIKAL</span><small>STOR</small></span>
                </a>
                <p>Dependable Philippine medical supplies for home care, daily hygiene, and family wellness.</p>
            </div>
            <div class="footer-links">
                <h2>Explore</h2>
                <a href="{{ route('shop.index') }}">Shop all</a>
                <a href="{{ route('home') }}#categories">Browse categories</a>
                <a href="{{ route('home') }}#about">Our approach</a>
                <a href="{{ route('contact.show') }}">Contact</a>
                <a href="{{ route('policies.terms') }}">Terms</a>
                <a href="{{ route('policies.privacy') }}">Privacy</a>
                <a href="{{ route('policies.returns') }}">Returns</a>
            </div>
            <div class="footer-note">
                <span class="eyebrow">A helpful note</span>
                <p>Product details and instructions should always be checked on the manufacturer's packaging. This shop is not a source of medical advice.</p>
            </div>
        </div>
        <div class="footer-bottom container-xl">
            <span>&copy; {{ date('Y') }} PILIPINS MEDIKAL STOR</span>
            <span>Prices shown in Philippine pesos (PHP)</span>
        </div>
    </footer>
</body>
</html>