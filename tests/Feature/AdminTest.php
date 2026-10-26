<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_routes_are_hidden_from_guests_and_customers(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));

        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_authorized_admin_can_view_dashboard_and_products(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Overview');

        $this->get(route('admin.products.index'))->assertOk()->assertSee('Products');
        $this->get(route('admin.customers.index'))->assertOk()->assertSee('Customers');
        $this->get(route('admin.reports.index'))->assertOk()->assertSee('Sales by day');
    }

    public function test_cancelling_an_order_restores_reserved_stock_once(): void
    {
        [$product, $order] = $this->orderWithProduct(stock: 2, reserved: 3);
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->patch(route('admin.orders.status', $order), ['status' => 'cancelled'])
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertSame(5, $product->fresh()->stock);

        $this->patch(route('admin.orders.status', $order), ['status' => 'cancelled'])
            ->assertSessionHasErrors('status');

        $this->assertSame(5, $product->fresh()->stock);
    }

    public function test_invalid_order_transition_does_not_change_status_or_inventory(): void
    {
        [$product, $order] = $this->orderWithProduct(stock: 2, reserved: 3);
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->patch(route('admin.orders.status', $order), ['status' => 'delivered'])
            ->assertSessionHasErrors('status');

        $this->assertSame('pending', $order->fresh()->status);
        $this->assertSame(2, $product->fresh()->stock);
    }

    private function orderWithProduct(int $stock, int $reserved): array
    {
        $category = Category::create(['name' => 'First Aid', 'slug' => 'first-aid', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Bandage Set',
            'slug' => 'bandage-set',
            'description' => 'Follow package instructions.',
            'price' => '30.00',
            'stock' => $stock,
            'is_active' => true,
        ]);
        $customer = User::factory()->create();
        $order = Order::create([
            'order_number' => 'CW-' . Str::upper(Str::random(8)),
            'confirmation_token' => (string) Str::uuid(),
            'user_id' => $customer->id,
            'customer_name' => $customer->name,
            'email' => $customer->email,
            'phone' => '09171234567',
            'delivery_address' => '12 Sample Street',
            'city' => 'Quezon City',
            'province' => 'Metro Manila',
            'postal_code' => '1100',
            'subtotal' => (string) (30 * $reserved),
            'delivery_fee' => '0.00',
            'total' => (string) (30 * $reserved),
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
            'status' => 'pending',
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => '30.00',
            'quantity' => $reserved,
            'line_total' => (string) (30 * $reserved),
        ]);

        return [$product, $order];
    }
}