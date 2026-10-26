<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function home(): View
    {
        return view('storefront.home', [
            'categories' => Category::query()
                ->where('is_active', true)
                ->whereHas('availableProducts')
                ->withCount('availableProducts')
                ->orderBy('name')
                ->get(),
            'products' => Product::available()
                ->with('category')
                ->latest()
                ->take(4)
                ->get(),
        ]);
    }

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:140'],
            'min_price' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'max_price' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'sort' => ['nullable', 'in:newest,name,price_asc,price_desc'],
        ]);

        $products = Product::available()->with('category');

        if (! empty($filters['q'])) {
            $products->where('name', 'like', '%' . $filters['q'] . '%');
        }

        if (! empty($filters['category'])) {
            $products->whereHas('category', fn ($query) => $query
                ->where('slug', $filters['category'])
                ->where('is_active', true));
        }

        if (isset($filters['min_price'])) {
            $products->where('price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $products->where('price', '<=', $filters['max_price']);
        }

        match ($filters['sort'] ?? 'newest') {
            'name' => $products->orderBy('name'),
            'price_asc' => $products->orderBy('price'),
            'price_desc' => $products->orderByDesc('price'),
            default => $products->latest(),
        };

        return view('storefront.shop', [
            'products' => $products->paginate(12)->withQueryString(),
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(),
            'filters' => $filters,
        ]);
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_active && $product->stock > 0 && $product->category->is_active, 404);

        return view('storefront.product', [
            'product' => $product->load('category'),
            'relatedProducts' => Product::available()
                ->where('category_id', $product->category_id)
                ->whereKeyNot($product->id)
                ->latest()
                ->take(4)
                ->get(),
        ]);
    }
}