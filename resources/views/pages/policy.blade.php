@extends('layouts.storefront')

@section('title', $title)

@section('content')
<section class="page-intro"><div class="container-xl"><span class="eyebrow">SHOP INFORMATION</span><h1>{{ $title }}</h1><p>Editable draft. The business owner should review and complete this page before launch.</p></div></section>
<section class="section-pad"><article class="container-xl policy-content"><p><strong>Last updated:</strong> {{ now()->format('F j, Y') }}</p>@foreach ($sections as $heading => $copy)<section><h2>{{ $heading }}</h2><p>{{ $copy }}</p></section>@endforeach</article></section>
@endsection