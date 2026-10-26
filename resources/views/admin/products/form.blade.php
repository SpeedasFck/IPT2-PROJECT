@extends('admin.layout')

@section('title', $product->exists ? 'Edit product' : 'Add product')

@section('content')
<div class="admin-page-heading"><div><span class="eyebrow">CATALOGUE</span><h1>{{ $product->exists ? 'Edit product' : 'Add product' }}</h1></div><a class="text-link" href="{{ route('admin.products.index') }}"><i class="bi bi-arrow-left" aria-hidden="true"></i> Back to products</a></div>
<form class="admin-panel admin-form" method="POST" enctype="multipart/form-data" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}">@csrf @if ($product->exists)@method('PUT')@endif
    @if ($errors->any())<div class="form-alert" role="alert">Please correct the fields marked below.</div>@endif
    <div class="admin-form-grid">
        <label>Product name<input name="name" value="{{ old('name', $product->name) }}" maxlength="180" required>@error('name')<small class="field-error">{{ $message }}</small>@enderror</label>
        <label>Category<select name="category_id" required><option value="">Select category</option>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>@endforeach</select>@error('category_id')<small class="field-error">{{ $message }}</small>@enderror</label>
        <label>SKU <span class="muted-note">(optional)</span><input name="sku" value="{{ old('sku', $product->sku) }}" maxlength="80">@error('sku')<small class="field-error">{{ $message }}</small>@enderror</label>
        <label>Price (PHP)<input type="number" name="price" min="0.01" max="99999999.99" step="0.01" value="{{ old('price', $product->price) }}" required>@error('price')<small class="field-error">{{ $message }}</small>@enderror</label>
        <label>Stock quantity<input type="number" name="stock" min="0" max="10000000" value="{{ old('stock', $product->stock ?? 0) }}" required>@error('stock')<small class="field-error">{{ $message }}</small>@enderror</label>
        <label>Low-stock threshold<input type="number" name="low_stock_threshold" min="0" value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? 5) }}" required>@error('low_stock_threshold')<small class="field-error">{{ $message }}</small>@enderror</label>
        <label class="field-wide">Description<textarea name="description" rows="5" maxlength="10000" required>{{ old('description', $product->description) }}</textarea>@error('description')<small class="field-error">{{ $message }}</small>@enderror</label>
        <label class="field-wide">Product image <span class="muted-note">JPG, PNG, or WebP; up to 5 MB</span><input type="file" name="image" accept="image/jpeg,image/png,image/webp" @required(!$product->exists)>@error('image')<small class="field-error">{{ $message }}</small>@enderror</label>
        @if ($product->image_url)<div class="current-image"><img src="{{ str_starts_with($product->image_url, 'http') ? $product->image_url : \Illuminate\Support\Facades\Storage::disk('public')->url($product->image_url) }}" alt="Current image for {{ $product->name }}"></div>@endif
        <label class="admin-checkbox"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->exists ? $product->is_active : true))> Active and visible to customers</label>
    </div>
    <div class="admin-form-actions"><button class="button-primary" type="submit">{{ $product->exists ? 'Save changes' : 'Create product' }}</button><a class="button-outline" href="{{ route('admin.products.index') }}">Cancel</a></div>
</form>
@endsection