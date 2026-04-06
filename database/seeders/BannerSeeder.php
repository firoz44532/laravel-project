<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Banner;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banners = [
            [
                'title' => 'Mega Sale Up To 70% OFF',
                'description' => 'Biggest sale of the year! Grab amazing deals on electronics, fashion, groceries and more.',
                'image' => 'https://via.placeholder.com/1200x400/FF6900/FFFFFF?text=Mega+Sale+70%25+OFF',
                'link' => '/products',
                'position' => 'hero',
                'sort_order' => 1,
            ],
            [
                'title' => 'Electronics Special',
                'description' => 'Latest smartphones and laptops at unbeatable prices',
                'image' => 'https://via.placeholder.com/1200x400/00BFA5/FFFFFF?text=Electronics+Special',
                'link' => '/category/electronics',
                'position' => 'hero',
                'sort_order' => 2,
            ],
            [
                'title' => 'Fashion Week',
                'description' => 'Trendy clothing and accessories for the modern you',
                'image' => 'https://via.placeholder.com/1200x400/FF4757/FFFFFF?text=Fashion+Week',
                'link' => '/category/fashion',
                'position' => 'hero',
                'sort_order' => 3,
            ],
        ];

        foreach ($banners as $banner) {
            Banner::create(array_merge($banner, ['is_active' => true]));
        }
    }
}
