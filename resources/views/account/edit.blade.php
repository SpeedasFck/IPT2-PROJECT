@extends('layouts.storefront')

@section('title', 'Account details')

@section('content')
<section class="page-intro"><div class="container-xl"><span class="eyebrow">YOUR CAREWELL ACCOUNT</span><h1>Account details</h1><p>Update the contact and delivery information for your account.</p></div></section>
<section class="section-pad"><div class="account-edit-layout container-xl">
    <div class="account-form-wrap account-edit-panel">
        @if (session('status'))<div class="status-alert" role="status">{{ session('status') }}</div>@endif
        <h2>Contact and delivery</h2>
        <form method="POST" action="{{ route('account.update') }}" class="account-form">@csrf @method('PUT')
            <label for="name">Full name</label><input id="name" name="name" value="{{ old('name', auth()->user()->name) }}" autocomplete="name" maxlength="120" required>@error('name')<small class="field-error">{{ $message }}</small>@enderror
            <label for="phone">Philippine mobile number</label><input id="phone" name="phone" type="tel" value="{{ old('phone', auth()->user()->phone) }}" placeholder="09XXXXXXXXX" autocomplete="tel">@error('phone')<small class="field-error">{{ $message }}</small>@enderror
            <label for="delivery_address">Street, building, barangay</label><textarea id="delivery_address" name="delivery_address" rows="3" autocomplete="street-address">{{ old('delivery_address', auth()->user()->delivery_address) }}</textarea>@error('delivery_address')<small class="field-error">{{ $message }}</small>@enderror
            <label for="city">City or municipality</label><input id="city" name="city" value="{{ old('city', auth()->user()->city) }}" autocomplete="address-level2">@error('city')<small class="field-error">{{ $message }}</small>@enderror
            <label for="province">Province</label><input id="province" name="province" value="{{ old('province', auth()->user()->province) }}" autocomplete="address-level1">@error('province')<small class="field-error">{{ $message }}</small>@enderror
            <label for="postal_code">Postal code</label><input id="postal_code" name="postal_code" value="{{ old('postal_code', auth()->user()->postal_code) }}" inputmode="numeric" maxlength="4">@error('postal_code')<small class="field-error">{{ $message }}</small>@enderror
            <button class="button-primary" type="submit">Save details <i class="bi bi-check2" aria-hidden="true"></i></button>
        </form>
    </div>
    <div class="account-form-wrap account-edit-panel">
        <h2>Change password</h2>
        <form method="POST" action="{{ route('account.password') }}" class="account-form">@csrf @method('PUT')
            <label for="current_password">Current password</label><input id="current_password" name="current_password" type="password" autocomplete="current-password" required>@error('current_password')<small class="field-error">{{ $message }}</small>@enderror
            <label for="password">New password</label><input id="password" name="password" type="password" autocomplete="new-password" required>@error('password')<small class="field-error">{{ $message }}</small>@enderror
            <label for="password_confirmation">Confirm new password</label><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
            <button class="button-primary" type="submit">Change password <i class="bi bi-key" aria-hidden="true"></i></button>
        </form>
    </div>
</div></section>
@endsection