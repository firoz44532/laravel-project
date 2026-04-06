<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShippingZone;
use App\Models\ShippingMethod;
use Illuminate\Support\Facades\DB;

class ShippingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default shipping methods
        $methods = [
            [
                'code' => 'standard',
                'name' => 'Standard Delivery',
                'description' => 'Regular delivery service with tracking',
                'estimated_days' => '2-5 business days',
                'base_cost' => 0,
                'is_active' => true,
                'tracking_available' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'express',
                'name' => 'Express Delivery',
                'description' => 'Fast delivery service with priority handling',
                'estimated_days' => '1-2 business days',
                'base_cost' => 0,
                'is_active' => true,
                'tracking_available' => true,
                'sort_order' => 2,
            ],
            [
                'code' => 'pickup',
                'name' => 'Store Pickup',
                'description' => 'Pick up from our store location',
                'estimated_days' => 'Same day',
                'base_cost' => 0,
                'is_active' => false, // Can be enabled if you have physical stores
                'tracking_available' => false,
                'sort_order' => 3,
            ],
        ];

        foreach ($methods as $method) {
            ShippingMethod::create($method);
        }

        // Create default shipping zones
        $zones = [
            [
                'code' => 'dhaka_metro',
                'name' => 'Dhaka Metro',
                'description' => 'Areas within Dhaka city',
                'default_cost' => 60,
                'express_cost' => 100,
                'delivery_days' => '1-2',
                'express_days' => '1',
                'is_active' => true,
                'sort_order' => 1,
                'cities' => ['Dhaka'],
                'areas' => [
                    'Dhanmondi', 'Gulshan', 'Banani', 'Baridhara', 'Mirpur', 
                    'Mohammadpur', 'Uttara', 'Farmgate', 'Shahbag', 'New Market',
                    'Bashundhara', 'Khilgaon', 'Rampura', 'Badda', 'Uttarkhan',
                    'Adabor', 'Shah Ali', 'Darussalam', 'Pallabi', 'Kafrul',
                    'Cantonment', 'Tejgaon', 'Sabujbagh', 'Ramna', 'Motijheel',
                    'Kotwali', 'Sutrapur', 'Lalbagh', 'Hazaribagh', 'Kamrangirchar',
                    'Dhanmondi', 'Kalabagan', 'Kamrangirchar'
                ],
            ],
            [
                'code' => 'outside_dhaka',
                'name' => 'Outside Dhaka',
                'description' => 'Major cities outside Dhaka',
                'default_cost' => 120,
                'express_cost' => 180,
                'delivery_days' => '2-3',
                'express_days' => '1-2',
                'is_active' => true,
                'sort_order' => 2,
                'cities' => [
                    'Chittagong', 'Sylhet', 'Rajshahi', 'Khulna', 'Barisal', 
                    'Rangpur', 'Mymensingh', 'Comilla', 'Narayanganj', 'Gazipur',
                    'Narsingdi', 'Manikganj', 'Munshiganj', 'Faridpur', 'Gopalganj',
                    'Madaripur', 'Shariatpur', 'Chandpur', 'Lakshmipur', 'Noakhali',
                    'Feni', 'Brahmanbaria', 'Kishoreganj', 'Tangail', 'Jamalpur',
                    'Sherpur', 'Netrokona', 'Kurigram', 'Lalmonirhat', 'Nilphamari',
                    'Rangpur', 'Dinajpur', 'Panchagarh', 'Thakurgaon', 'Pabna',
                    'Sirajganj', 'Bogura', 'Joypurhat', 'Naogaon', 'Chapainawabganj',
                    'Rajshahi', 'Natore', 'Chuadanga', 'Jhenaidah', 'Kushtia',
                    'Magura', 'Meherpur', 'Khulna', 'Bagerhat', 'Satkhira',
                    'Jessore', 'Jhalokathi', 'Barisal', 'Bhola', 'Patuakhali',
                    'Pirojpur', 'Barguna', 'Sylhet', 'Moulvibazar', 'Habiganj',
                    'Sunamganj', 'Chittagong', 'Cox\'s Bazar', 'Bandarban', 'Rangamati',
                    'Khagrachari'
                ],
                'areas' => [],
            ],
            [
                'code' => 'remote_areas',
                'name' => 'Remote Areas',
                'description' => 'Remote and hard-to-reach areas',
                'default_cost' => 150,
                'express_cost' => 250,
                'delivery_days' => '3-5',
                'express_days' => '2-3',
                'is_active' => true,
                'sort_order' => 3,
                'cities' => [
                    'Bandarban', 'Rangamati', 'Khagrachari', 'Cox\'s Bazar', 
                    'Patuakhali', 'Barguna', 'Jhalokathi', 'Pirojpur', 'Bhola',
                    'Lakshmipur', 'Noakhali', 'Feni', 'Chandpur', 'Madaripur',
                    'Shariatpur', 'Gopalganj', 'Sirajganj', 'Pabna', 'Joypurhat',
                    'Naogaon', 'Chapainawabganj', 'Meherpur', 'Chuadanga', 'Jhenaidah'
                ],
                'areas' => [],
            ],
        ];

        foreach ($zones as $zone) {
            $createdZone = ShippingZone::create($zone);
            
            // Attach methods to zones with rates
            $standardMethod = ShippingMethod::where('code', 'standard')->first();
            $expressMethod = ShippingMethod::where('code', 'express')->first();
            
            if ($standardMethod) {
                $createdZone->shippingMethods()->attach($standardMethod->id, [
                    'cost' => $zone['default_cost'],
                    'additional_cost_per_kg' => 0,
                    'free_shipping_threshold' => null,
                    'is_active' => true,
                ]);
            }
            
            if ($expressMethod) {
                $createdZone->shippingMethods()->attach($expressMethod->id, [
                    'cost' => $zone['express_cost'],
                    'additional_cost_per_kg' => 0,
                    'free_shipping_threshold' => null,
                    'is_active' => true,
                ]);
            }
        }

        $this->command->info('Default shipping zones and methods created successfully!');
    }
}
