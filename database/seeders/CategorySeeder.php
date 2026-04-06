<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics', 'slug' => 'electronics', 'description' => 'Mobile phones, laptops, and accessories', 'sort_order' => 1],
            ['name' => 'Fashion', 'slug' => 'fashion', 'description' => 'Clothing, shoes, and accessories', 'sort_order' => 2],
            ['name' => 'Home & Living', 'slug' => 'home-living', 'description' => 'Furniture, decor, and kitchen appliances', 'sort_order' => 3],
            ['name' => 'Beauty & Personal Care', 'slug' => 'beauty-personal-care', 'description' => 'Skincare, makeup, and personal care products', 'sort_order' => 4],
            ['name' => 'Groceries', 'slug' => 'groceries', 'description' => 'Food items, beverages, and daily essentials', 'sort_order' => 5],
            ['name' => 'Sports & Outdoors', 'slug' => 'sports-outdoors', 'description' => 'Sports equipment and outdoor gear', 'sort_order' => 6],
            ['name' => 'Books & Media', 'slug' => 'books-media', 'description' => 'Books, movies, and music', 'sort_order' => 7],
            ['name' => 'Toys & Games', 'slug' => 'toys-games', 'description' => 'Toys, games, and children products', 'sort_order' => 8],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create some subcategories for Electronics
        $electronics = Category::where('slug', 'electronics')->first();
        $subcategories = [
            ['name' => 'Mobile Phones', 'slug' => 'mobile-phones', 'parent_id' => $electronics->id, 'sort_order' => 1],
            ['name' => 'Laptops', 'slug' => 'laptops', 'parent_id' => $electronics->id, 'sort_order' => 2],
            ['name' => 'Tablets', 'slug' => 'tablets', 'parent_id' => $electronics->id, 'sort_order' => 3],
            ['name' => 'Accessories', 'slug' => 'electronics-accessories', 'parent_id' => $electronics->id, 'sort_order' => 4],
        ];

        foreach ($subcategories as $subcategory) {
            Category::create($subcategory);
        }
    }
}
