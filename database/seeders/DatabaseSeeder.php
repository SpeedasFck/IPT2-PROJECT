<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'First Aid & Emergency', 'slug' => 'first-aid', 'icon' => 'bi-bandaid'],
            ['name' => 'Medical PPE', 'slug' => 'personal-protection', 'icon' => 'bi-shield-check'],
            ['name' => 'Vital Signs', 'slug' => 'health-devices', 'icon' => 'bi-activity'],
            ['name' => 'Hygiene & Care', 'slug' => 'personal-care', 'icon' => 'bi-droplet'],
            ['name' => 'Pharmacy Essentials', 'slug' => 'vitamins-supplements', 'icon' => 'bi-capsule'],
            ['name' => 'Wound Care', 'slug' => 'wound-care', 'icon' => 'bi-heart-pulse'],
            ['name' => 'Hygiene', 'slug' => 'hygiene', 'icon' => 'bi-stars'],
            ['name' => 'Home Recovery', 'slug' => 'home-healthcare', 'icon' => 'bi-house-heart'],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::updateOrCreate(['slug' => $category['slug']], $category + ['is_active' => true]);
        }

        $products = [
            ['category' => 'first-aid', 'name' => 'Philippine Home First Aid Kit', 'slug' => 'compact-first-aid-kit', 'price' => '349.00', 'stock' => 18, 'image_url' => 'https://images.unsplash.com/photo-1603398938378-e54eab446dde?auto=format&fit=crop&w=900&q=85', 'description' => 'A compact emergency kit for common home and travel needs, including bandages, antiseptic, and basic care essentials for Filipino households.'],
            ['category' => 'personal-protection', 'name' => '3-Ply Surgical Face Masks', 'slug' => 'disposable-face-masks', 'price' => '129.00', 'stock' => 42, 'image_url' => 'https://images.unsplash.com/photo-1584634731339-252c581abfc5?auto=format&fit=crop&w=900&q=85', 'description' => 'Disposable 3-ply face masks designed for daily protection and public-use convenience. Check the packaging for certification and usage instructions.'],
            ['category' => 'health-devices', 'name' => 'Digital Clinical Thermometer', 'slug' => 'digital-thermometer', 'price' => '289.00', 'stock' => 12, 'image_url' => 'https://images.unsplash.com/photo-1584362917165-526a968579e8?auto=format&fit=crop&w=900&q=85', 'description' => 'An easy-to-read digital thermometer for home monitoring and daily temperature checks, ideal for households across the Philippines.'],
            ['category' => 'personal-care', 'name' => '70% Alcohol Hand Sanitizer', 'slug' => 'gentle-hand-sanitizer', 'price' => '89.00', 'stock' => 30, 'image_url' => 'https://images.unsplash.com/photo-1584744982491-665216d95f8b?auto=format&fit=crop&w=900&q=85', 'description' => 'A quick and practical sanitizer for everyday hand hygiene, made for busy homes, clinics, and workspaces.'],
            ['category' => 'wound-care', 'name' => 'Sterile Gauze & Bandage Pack', 'slug' => 'adhesive-bandage-set', 'price' => '75.00', 'stock' => 25, 'image_url' => 'https://images.unsplash.com/photo-1603398938378-e54eab446dde?auto=format&fit=crop&w=900&q=85', 'description' => 'An assortment of sterile dressings and adhesive bandages for cuts, scrapes, and minor wound protection.'],
            ['category' => 'home-healthcare', 'name' => 'Hot & Cold Therapy Wrap', 'slug' => 'reusable-hot-cold-pack', 'price' => '199.00', 'stock' => 9, 'image_url' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=900&q=85', 'description' => 'A reusable therapy wrap for soothing strained muscles or mild discomfort at home after activity or exertion.'],
        ];

        foreach ($products as $product) {
            $category = \App\Models\Category::where('slug', $product['category'])->firstOrFail();
            unset($product['category']);
            \App\Models\Product::updateOrCreate(
                ['slug' => $product['slug']],
                $product + ['category_id' => $category->id, 'is_active' => true, 'low_stock_threshold' => 5],
            );
        }
    }
}
