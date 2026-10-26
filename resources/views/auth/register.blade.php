@extends('layouts.storefront')

@section('title', 'Create account')

@section('content')
<section class="account-form-section section-pad">
    <div class="account-form-wrap">
        <span class="eyebrow">A LITTLE MORE CONVENIENT</span>
        <h1>Create your account</h1>
        <p class="account-form-intro">Keep track of your orders and contact details in one place.</p>
        @if ($errors->any())<div class="form-alert" role="alert">Please check the fields below.</div>@endif
        <form method="POST" action="{{ route('register.store') }}" class="account-form">
            @csrf
            <label for="name">Full name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" autocomplete="name" maxlength="120" required>
            @error('name')<span class="field-error">{{ $message }}</span>@enderror
            <label for="email">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
            @error('email')<span class="field-error">{{ $message }}</span>@enderror
            <label for="password">Password</label>
            <input id="password" type="password" name="password" autocomplete="new-password" required>
            @error('password')<span class="field-error">{{ $message }}</span>@enderror
            <label for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required>
            <button class="button-primary" type="submit">Create account <i class="bi bi-arrow-right" aria-hidden="true"></i></button>
        </form>
        <p class="account-switch">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
    </div>
</section>
@endsection