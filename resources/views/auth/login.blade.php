@extends('layouts.storefront')

@section('title', 'Sign in')

@section('content')
<section class="account-form-section section-pad">
    <div class="account-form-wrap">
        <span class="eyebrow">WELCOME BACK</span>
        <h1>Sign in to your account</h1>
        <p class="account-form-intro">View your orders and keep your details up to date.</p>
        @if ($errors->any())<div class="form-alert" role="alert">{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('login.store') }}" class="account-form">
            @csrf
            <label for="email">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
            @error('email')<span class="field-error">{{ $message }}</span>@enderror
            <label for="password">Password</label>
            <input id="password" type="password" name="password" autocomplete="current-password" required>
            @error('password')<span class="field-error">{{ $message }}</span>@enderror
            <label class="checkbox-label"><input type="checkbox" name="remember" value="1"> Remember me</label>
            <button class="button-primary" type="submit">Sign in <i class="bi bi-arrow-right" aria-hidden="true"></i></button>
        </form>
        <p class="account-switch">New here? <a href="{{ route('register') }}">Create an account</a></p>
    </div>
</section>
@endsection