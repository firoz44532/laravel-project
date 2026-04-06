<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'slug' => 'samsung-galaxy-s24-ultra',
                'description' => 'Latest flagship smartphone with advanced camera system and S Pen',
                'short_description' => '6.8" Dynamic AMOLED, 200MP camera, Snapdragon 8 Gen 3',
                'sku' => 'SAMSUNG-S24U-256',
                'price' => 145000.00,
                'compare_price' => 165000.00,
                'stock_quantity' => 50,
                'weight' => 0.234,
                'category_slug' => 'mobile-phones',
            ],
            [
                'name' => 'iPhone 15 Pro Max',
                'slug' => 'iphone-15-pro-max',
                'description' => 'Apple premium smartphone with titanium design and A17 Pro chip',
                'short_description' => '6.7" Super Retina XDR, 48MP camera, A17 Pro chip',
                'sku' => 'APPLE-IP15PM-256',
                'price' => 185000.00,
                'compare_price' => 195000.00,
                'stock_quantity' => 30,
                'weight' => 0.221,
                'category_slug' => 'mobile-phones',
            ],
            [
                'name' => 'MacBook Pro 14"',
                'slug' => 'macbook-pro-14',
                'description' => 'Professional laptop with M3 Pro chip and stunning Liquid Retina XDR display',
                'short_description' => '14.2" Liquid Retina XDR, M3 Pro, 18GB RAM, 512GB SSD',
                'sku' => 'APPLE-MBP14-M3P',
                'price' => 285000.00,
                'compare_price' => null,
                'stock_quantity' => 15,
                'weight' => 1.6,
                'category_slug' => 'laptops',
            ],
            [
                'name' => 'Sony WH-1000XM5 Headphones',
                'slug' => 'sony-wh-1000xm5',
                'description' => 'Industry leading noise canceling wireless headphones',
                'short_description' => 'Wireless, Noise Canceling, 30hr battery, Hi-Res Audio',
                'sku' => 'SONY-WH1000XM5',
                'price' => 35000.00,
                'compare_price' => 45000.00,
                'stock_quantity' => 100,
                'weight' => 0.25,
                'category_slug' => 'electronics-accessories',
            ],
            [
                'name' => 'iPad Air 5th Gen',
                'slug' => 'ipad-air-5th-gen',
                'description' => 'Versatile tablet with M1 chip and 10.9" Liquid Retina display',
                'short_description' => '10.9" Liquid Retina, M1 chip, 64GB, Wi-Fi',
                'sku' => 'APPLE-IPA5-64',
                'price' => 75000.00,
                'compare_price' => 85000.00,
                'stock_quantity' => 40,
                'weight' => 0.461,
                'category_slug' => 'tablets',
            ],
            [
                'name' => 'Samsung 55" QLED 4K Smart TV',
                'slug' => 'samsung-55-qled-4k-tv',
                'description' => 'Premium QLED TV with Quantum HDR and Smart TV features',
                'short_description' => '55" QLED, 4K UHD, Smart TV, Quantum HDR',
                'sku' => 'SAMSUNG-QLED55-4K',
                'price' => 95000.00,
                'compare_price' => 115000.00,
                'stock_quantity' => 20,
                'weight' => 17.5,
                'category_slug' => 'electronics',
            ],
            [
                'name' => 'Nike Air Max 270',
                'slug' => 'nike-air-max-270',
                'description' => 'Comfortable running shoes with Max Air unit',
                'short_description' => 'Running shoes, Max Air unit, Breathable mesh',
                'sku' => 'NIKE-AM270-BLK',
                'price' => 12000.00,
                'compare_price' => 15000.00,
                'stock_quantity' => 200,
                'weight' => 0.3,
                'category_slug' => 'fashion',
            ],
            [
                'name' => 'Canon EOS R6 Mark II',
                'slug' => 'canon-eos-r6-mark-ii',
                'description' => 'Professional mirrorless camera with advanced autofocus',
                'short_description' => '24.2MP Full Frame, 4K 60fps, Advanced AF',
                'sku' => 'CANON-EOSR6M2',
                'price' => 285000.00,
                'compare_price' => null,
                'stock_quantity' => 10,
                'weight' => 0.688,
                'category_slug' => 'electronics-accessories',
            ],
        ];

        foreach ($products as $productData) {
            $category = Category::where('slug', $productData['category_slug'])->first();
            
            $product = Product::create([
                'name' => $productData['name'],
                'slug' => $productData['slug'],
                'description' => $productData['description'],
                'short_description' => $productData['short_description'],
                'sku' => $productData['sku'],
                'price' => $productData['price'],
                'compare_price' => $productData['compare_price'],
                'stock_quantity' => $productData['stock_quantity'],
                'weight' => $productData['weight'],
                'category_id' => $category->id,
                'is_active' => true,
                'is_featured' => rand(0, 1) == 1,
                'sort_order' => rand(1, 100),
            ]);

            // Create product images
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => 'https://via.placeholder.com/400x400',
                'alt_text' => $product->name,
                'sort_order' => 1,
                'is_primary' => true,
            ]);
        }
    }
}
