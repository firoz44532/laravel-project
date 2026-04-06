<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Apple',
                'slug' => 'apple',
                'description' => 'Technology company that designs, develops, and sells consumer electronics, computer software, and online services.',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Samsung',
                'slug' => 'samsung',
                'description' => 'South Korean multinational conglomerate specializing in electronics, telecommunications, and home appliances.',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Sony',
                'slug' => 'sony',
                'description' => 'Japanese multinational conglomerate corporation specializing in electronics, gaming, entertainment, and financial services.',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Nike',
                'slug' => 'nike',
                'description' => 'American multinational corporation that designs, develops, manufactures, and markets footwear, apparel, equipment, and accessories.',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 4,
            ],
            [
                'name' => 'Adidas',
                'slug' => 'adidas',
                'description' => 'German multinational corporation that designs and manufactures shoes, clothing, and accessories.',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 5,
            ],
            [
                'name' => 'Dell',
                'slug' => 'dell',
                'description' => 'American multinational technology company that develops, sells, repairs, and supports computers and related products and services.',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 6,
            ],
            [
                'name' => 'HP',
                'slug' => 'hp',
                'description' => 'American multinational information technology company that develops personal computers, printers, and related supplies.',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 7,
            ],
            [
                'name' => 'LG',
                'slug' => 'lg',
                'description' => 'South Korean multinational electronics company that manufactures electronics, chemicals, and telecom products.',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 8,
            ],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}
