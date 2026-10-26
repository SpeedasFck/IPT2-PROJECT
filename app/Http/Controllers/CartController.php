<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $cart = $request->session()->get('cart', []);
        $products = Product::query()->where('is_active', true)->with('category')->whereKey(array_keys($cart))->get()->keyBy('id');
        $items = collect($cart)->map(function ($quantity, $productId) use ($products) {
            $product = $products->get($productId);

            return $product && $product->category->is_active
                ? ['product' => $product, 'quantity' => (int) $quantity]
                : null;
        })->filter();

        return view('cart.index', [
            'items' => $items,
            'subtotal' => $items->sum(fn ($item) => (float) $item['product']->price * $item['quantity']),
        ]);
    }

    public function add(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:99']]);
        abort_unless($product->is_active && $product->stock > 0 && $product->category->is_active, 404);

        $cart = $request->session()->get('cart', []);
        $quantity = ($cart[$product->id] ?? 0) + $validated['quantity'];

        if ($quantity > $product->stock) {
            return back()->withErrors(['cart' => 'The requested quantity is higher than the available stock.']);
        }

        $cart[$product->id] = $quantity;
        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('status', 'Added to your cart.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:99']]);
        $cart = $request->session()->get('cart', []);

        if (! array_key_exists($product->id, $cart)) {
            return redirect()->route('cart.index');
        }

        if (! $product->is_active || $product->stock < $validated['quantity']) {
            return back()->withErrors(['cart' => 'The requested quantity is higher than the available stock.']);
        }

        $cart[$product->id] = $validated['quantity'];
        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('status', 'Cart updated.');
    }

    public function remove(Request $request, Product $product): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[$product->id]);
        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('status', 'Item removed.');
    }

    public function clear(Request $request): RedirectResponse
    {
        $request->session()->forget('cart');

        return redirect()->route('cart.index')->with('status', 'Your cart is clear.');
    }
}