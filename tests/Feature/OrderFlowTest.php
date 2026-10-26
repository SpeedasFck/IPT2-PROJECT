<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_place_cod_order_using_server_price_and_stock_is_decremented(): void
    {
        $product = $this->product(stock: 5);

        $response = $this->withSession(['cart' => [$product->id => 2]])
            ->post(route('checkout.store'), $this->checkoutData() + ['price' => '0.01']);
        $response->assertSessionHasNoErrors();

        $order = Order::with('items')->firstOrFail();

        $this->assertGuest();
        $this->assertSame('20.00', $order->subtotal);
        $this->assertSame('20.00', $order->total);
        $this->assertSame('10.00', $order->items->first()->unit_price);
        $this->assertSame('First Aid Item', $order->items->first()->product_name);
        $this->assertSame(3, $product->fresh()->stock);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'payment_method' => 'cod', 'status' => 'pending']);
        $response->assertSessionMissing('cart');
        $this->get(route('orders.confirmation', $order->confirmation_token))->assertOk()->assertSee($order->order_number);
    }

    public function test_checkout_rejects_overselling_without_creating_an_order(): void
    {
        $product = $this->product(stock: 2);

        $this->withSession(['cart' => [$product->id => 3]])
            ->post(route('checkout.store'), $this->checkoutData())
            ->assertRedirect(route('cart.index'))
            ->assertSessionHasErrors('cart');

        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(2, $product->fresh()->stock);
    }

    public function test_checkout_rejects_invalid_philippine_phone_and_postal_code(): void
    {
        $product = $this->product(stock: 3);

        $this->withSession(['cart' => [$product->id => 1]])
            ->post(route('checkout.store'), $this->checkoutData(['phone' => '12345', 'postal_code' => '12']))
            ->assertSessionHasErrors(['phone', 'postal_code']);

        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(3, $product->fresh()->stock);
    }

    public function test_cart_rejects_quantity_above_current_stock(): void
    {
        $product = $this->product(stock: 2);

        $response = $this->withSession(['cart' => []])
            ->post(route('cart.add', $product), ['quantity' => 3])
            ->assertSessionHasErrors('cart');

        $response->assertSessionHas('cart', []);
    }

    public function test_customer_account_lists_only_the_signed_in_customers_orders(): void
    {
        $customer = User::factory()->create();
        $otherCustomer = User::factory()->create();
        $order = $this->orderFor($customer, 'CW-OWNED-1');
        $this->orderFor($otherCustomer, 'CW-OWNED-2');

        $this->actingAs($customer)
            ->get(route('account.show'))
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertDontSee('CW-OWNED-2');
    }

    public function test_confirmation_token_does_not_expose_sequential_order_id(): void
    {
        $customer = User::factory()->create();
        $order = $this->orderFor($customer, 'CW-PRIVATE-1');

        $this->get(route('orders.confirmation', $order->confirmation_token))
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertDontSee('/orders/confirmation/' . $order->id);
    }

    private function product(int $stock): Product
    {
        $category = Category::create(['name' => 'First Aid', 'slug' => 'first-aid', 'is_active' => true]);

        return Product::create([
            'category_id' => $category->id,
            'name' => 'First Aid Item',
            'slug' => 'first-aid-item',
            'description' => 'Follow package instructions.',
            'price' => '10.00',
            'stock' => $stock,
            'is_active' => true,
        ]);
    }

    private function orderFor(User $user, string $orderNumber): Order
    {
        return Order::create([
            'order_number' => $orderNumber,
            'confirmation_token' => (string) \Illuminate\Support\Str::uuid(),
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'email' => $user->email,
            'phone' => '09171234567',
            'delivery_address' => '12 Sample Street',
            'city' => 'Quezon City',
            'province' => 'Metro Manila',
            'postal_code' => '1100',
            'subtotal' => '10.00',
            'delivery_fee' => '0.00',
            'total' => '10.00',
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
            'status' => 'pending',
        ]);
    }

    private function checkoutData(array $overrides = []): array
    {
        return array_merge([
            'customer_name' => 'Casey Shopper',
            'email' => 'casey@example.test',
            'phone' => '09171234567',
            'delivery_address' => '12 Sample Street, Barangay Central',
            'city' => 'Quezon City',
            'province' => 'Metro Manila',
            'postal_code' => '1100',
            'payment_method' => 'cod',
        ], $overrides);
    }
}