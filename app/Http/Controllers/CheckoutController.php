<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;

class CheckoutController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $items = $this->availableCart($request);

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Add an available product before checkout.']);
        }

        if ($items->contains(fn ($item) => $item['quantity'] > $item['product']->stock || $item['product']->stock < 1)) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'A product in your cart is no longer available in that quantity. Update or remove it before checkout.']);
        }

        return view('checkout.create', [
            'items' => $items,
            'subtotal' => $items->sum(fn ($item) => (float) $item['product']->price * $item['quantity']),
            'deliveryFee' => config('shop.delivery_fee'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'regex:/^(?:\+?63|0)9\d{9}$/'],
            'delivery_address' => ['required', 'string', 'max:1000'],
            'city' => ['required', 'string', 'max:100'],
            'province' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'regex:/^\d{4}$/'],
            'payment_method' => ['required', 'in:cod'],
        ]);

        $cart = $request->session()->get('cart', []);

        if ($cart === []) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'Your cart is empty.']);
        }

        try {
            $order = DB::transaction(function () use ($cart, $validated, $request) {
                $products = Product::query()
                    ->whereKey(array_keys($cart))
                    ->where('is_active', true)
                    ->whereHas('category', fn ($query) => $query->where('is_active', true))
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                if ($products->count() !== count($cart)) {
                    throw new RuntimeException('A product in your cart is no longer available. Please review your cart.');
                }

                $subtotalCents = 0;
                foreach ($cart as $productId => $quantity) {
                    $product = $products->get($productId);
                    $quantity = (int) $quantity;

                    if (! $product || $quantity < 1 || $product->stock < $quantity) {
                        throw new RuntimeException('A product in your cart no longer has enough stock. Please review your cart.');
                    }

                    $subtotalCents += (int) round((float) $product->price * 100) * $quantity;
                }

                $deliveryFeeCents = (int) round((float) config('shop.delivery_fee') * 100);
                $order = Order::create([
                    'order_number' => 'CW-' . now()->format('ymd') . '-' . Str::upper(Str::random(6)),
                    'confirmation_token' => (string) Str::uuid(),
                    'user_id' => $request->user()?->id,
                    'customer_name' => $validated['customer_name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'delivery_address' => $validated['delivery_address'],
                    'city' => $validated['city'],
                    'province' => $validated['province'],
                    'postal_code' => $validated['postal_code'],
                    'subtotal' => number_format($subtotalCents / 100, 2, '.', ''),
                    'delivery_fee' => number_format($deliveryFeeCents / 100, 2, '.', ''),
                    'total' => number_format(($subtotalCents + $deliveryFeeCents) / 100, 2, '.', ''),
                    'payment_method' => 'cod',
                    'payment_status' => 'unpaid',
                    'status' => 'pending',
                ]);

                foreach ($cart as $productId => $quantity) {
                    $product = $products->get($productId);
                    $quantity = (int) $quantity;
                    $updated = Product::query()->whereKey($product->id)->where('stock', '>=', $quantity)->decrement('stock', $quantity);

                    if ($updated !== 1) {
                        throw new RuntimeException('Stock changed while placing your order. Please review your cart.');
                    }

                    $lineCents = (int) round((float) $product->price * 100) * $quantity;
                    $order->items()->create([
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'unit_price' => $product->price,
                        'quantity' => $quantity,
                        'line_total' => number_format($lineCents / 100, 2, '.', ''),
                    ]);
                }

                return $order;
            }, 3);
        } catch (RuntimeException $exception) {
            return redirect()->route('cart.index')->withErrors(['cart' => $exception->getMessage()]);
        } catch (QueryException) {
            return redirect()->route('cart.index')->withErrors(['cart' => 'We could not place your order just now. Please review your cart and try again.']);
        }

        $request->session()->forget('cart');

        return redirect()->route('orders.confirmation', $order->confirmation_token);
    }

    public function confirmation(Order $order): View
    {
        return view('checkout.confirmation', ['order' => $order->load('items')]);
    }

    private function availableCart(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        $products = Product::query()->where('is_active', true)->with('category')->whereKey(array_keys($cart))->get()->keyBy('id');

        return collect($cart)->map(function ($quantity, $productId) use ($products) {
            $product = $products->get($productId);

            return $product && $product->category->is_active
                ? ['product' => $product, 'quantity' => (int) $quantity]
                : null;
        })->filter();
    }
}