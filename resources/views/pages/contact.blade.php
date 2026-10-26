@extends('layouts.storefront')

@section('title', 'Contact us')

@section('content')
<section class="page-intro"><div class="container-xl"><span class="eyebrow">WE'RE HERE TO HELP</span><h1>Get in touch</h1><p>Send a message to the shop. Contact details shown below are placeholders until the business provides its information.</p></div></section>
<section class="section-pad"><div class="container-xl contact-layout">
    <div class="contact-info"><span class="eyebrow">SHOP INFORMATION</span><h2>Questions about an order or product?</h2><p>Use the form and the shop team can follow up using the contact details you provide.</p><dl><dt>Email</dt><dd>Shop email to be provided</dd><dt>Phone</dt><dd>Shop phone to be provided</dd><dt>Address</dt><dd>Business address to be provided</dd><dt>Hours</dt><dd>Business hours to be provided</dd></dl></div>
    <form class="contact-form" method="POST" action="{{ route('contact.store') }}">@csrf
        @if (session('status'))<div class="status-alert" role="status">{{ session('status') }}</div>@endif
        @if ($errors->any())<div class="form-alert" role="alert">Please correct the fields below.</div>@endif
        <label>Your name<input name="name" value="{{ old('name') }}" maxlength="120" autocomplete="name" required>@error('name')<small class="field-error">{{ $message }}</small>@enderror</label>
        <label>Email address<input name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>@error('email')<small class="field-error">{{ $message }}</small>@enderror</label>
        <label>Subject<input name="subject" value="{{ old('subject') }}" maxlength="180" required>@error('subject')<small class="field-error">{{ $message }}</small>@enderror</label>
        <label>Message<textarea name="message" rows="6" maxlength="5000" required>{{ old('message') }}</textarea>@error('message')<small class="field-error">{{ $message }}</small>@enderror</label>
        <button class="button-primary" type="submit">Send message <i class="bi bi-arrow-right" aria-hidden="true"></i></button>
    </form>
</div></section>
@endsection