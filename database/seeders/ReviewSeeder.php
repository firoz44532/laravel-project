<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Product;
use App\Models\User;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::take(5)->get();
        $users = User::take(3)->get();

        $sampleReviews = [
            [
                'title' => 'Excellent Product!',
                'comment' => 'Really satisfied with this purchase. Quality is amazing and delivery was fast.',
                'rating' => 5,
            ],
            [
                'title' => 'Good Value for Money',
                'comment' => 'Product meets expectations. Good quality for the price point.',
                'rating' => 4,
            ],
            [
                'title' => 'Average Experience',
                'comment' => 'Product is okay, nothing extraordinary but does the job.',
                'rating' => 3,
            ],
            [
                'title' => 'Could Be Better',
                'comment' => 'Product quality could be improved. Not worth the price.',
                'rating' => 2,
            ],
            [
                'title' => 'Disappointed',
                'comment' => 'Poor quality product. Would not recommend.',
                'rating' => 1,
            ],
        ];

        foreach ($products as $product) {
            foreach ($users as $user) {
                $reviewData = $sampleReviews[array_rand($sampleReviews)];
                
                Review::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'rating' => $reviewData['rating'],
                    'title' => $reviewData['title'],
                    'comment' => $reviewData['comment'],
                    'is_approved' => rand(0, 1) == 1,
                    'is_verified_purchase' => true,
                ]);
            }
        }
    }
}
