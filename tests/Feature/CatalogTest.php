<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_lists_active_categories_and_available_products(): void
    {
        $category = Category::create(['name' => 'First Aid', 'slug' => 'first-aid', 'is_active' => true]);
        Product::create([
            'category_id' => $category->id,
            'name' => 'Bandage Set',
            'slug' => 'bandage-set',
            'description' => 'Follow package directions.',
            'price' => '75.00',
            'stock' => 4,
            'is_active' => true,
        ]);
        Product::create([
            'category_id' => $category->id,
            'name' => 'Unavailable Gauze',
            'slug' => 'unavailable-gauze',
            'description' => 'Follow package directions.',
            'price' => '99.00',
            'stock' => 0,
            'is_active' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Bandage Set')
            ->assertDontSee('Unavailable Gauze')
            ->assertSee('First Aid');
    }

    public function test_shop_search_and_price_filters_are_applied(): void
    {
        $category = Category::create(['name' => 'Personal Care', 'slug' => 'personal-care', 'is_active' => true]);
        Product::create([
            'category_id' => $category->id,
            'name' => 'Hand Sanitizer',
            'slug' => 'hand-sanitizer',
            'description' => 'Check the product label.',
            'price' => '89.00',
            'stock' => 3,
            'is_active' => true,
        ]);
        Product::create([
            'category_id' => $category->id,
            'name' => 'Care Lotion',
            'slug' => 'care-lotion',
            'description' => 'Check the product label.',
            'price' => '189.00',
            'stock' => 3,
            'is_active' => true,
        ]);

        $this->get(route('shop.index', ['q' => 'sanitizer', 'max_price' => '100']))
            ->assertOk()
            ->assertSee('Hand Sanitizer')
            ->assertDontSee('Care Lotion');
    }

    public function test_unavailable_product_details_are_not_public(): void
    {
        $category = Category::create(['name' => 'First Aid', 'slug' => 'first-aid', 'is_active' => true]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Out of Stock Item',
            'slug' => 'out-of-stock-item',
            'description' => 'Follow package directions.',
            'price' => '55.00',
            'stock' => 0,
            'is_active' => true,
        ]);

        $this->get(route('products.show', $product))->assertNotFound();
    }
}