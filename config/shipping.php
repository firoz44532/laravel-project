<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Shipping Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for shipping zones, methods, rates,
    | tax calculations, and other shipping-related settings.
    |
    */

    'default_shipping_cost' => 50,
    
    'free_shipping_threshold' => 2000,
    
    'tax' => [
        'enabled' => true,
        'vat_rate' => 15, // 15% VAT
        'shipping_taxable' => true, // Whether to apply tax on shipping
        'tax_inclusive' => false, // Whether prices include tax
    ],

    'zones' => [
        'dhaka_metro' => [
            'name' => 'Dhaka Metro',
            'description' => 'Areas within Dhaka city',
            'default_cost' => 60,
            'express_cost' => 100,
            'delivery_days' => '1-2',
            'express_days' => '1',
            'cities' => ['Dhaka'],
            'areas' => [
                'Dhanmondi', 'Gulshan', 'Banani', 'Baridhara', 'Mirpur', 
                'Mohammadpur', 'Uttara', 'Farmgate', 'Shahbag', 'New Market',
                'Bashundhara', 'Khilgaon', 'Rampura', 'Badda', 'Uttarkhan'
            ],
        ],
        
        'outside_dhaka' => [
            'name' => 'Outside Dhaka',
            'description' => 'Areas outside Dhaka city',
            'default_cost' => 120,
            'express_cost' => 180,
            'delivery_days' => '2-3',
            'express_days' => '1-2',
            'cities' => [
                'Chittagong', 'Sylhet', 'Rajshahi', 'Khulna', 'Barisal', 
                'Rangpur', 'Mymensingh', 'Comilla', 'Narayanganj', 'Gazipur'
            ],
        ],
        
        'remote_areas' => [
            'name' => 'Remote Areas',
            'description' => 'Remote and hard-to-reach areas',
            'default_cost' => 150,
            'express_cost' => 250,
            'delivery_days' => '3-5',
            'express_days' => '2-3',
            'cities' => [
                'Bandarban', 'Rangamati', 'Khagrachari', 'Cox\'s Bazar', 
                'Patuakhali', 'Barguna', 'Jhalokathi', 'Pirojpur', 'Bhola',
                'Lakshmipur', 'Noakhali', 'Feni', 'Chandpur', 'Madaripur',
                'Shariatpur', 'Gopalganj', 'Sirajganj', 'Pabna', 'Joypurhat',
                'Naogaon', 'Chapainawabganj', 'Meherpur', 'Chuadanga', 'Jhenaidah'
            ],
        ],
    ],

    'methods' => [
        'standard' => [
            'name' => 'Standard Delivery',
            'description' => 'Regular delivery service',
            'estimated_days' => '2-5 business days',
            'tracking' => true,
            'active' => true,
        ],
        
        'express' => [
            'name' => 'Express Delivery',
            'description' => 'Fast delivery service',
            'estimated_days' => '1-2 business days',
            'tracking' => true,
            'active' => true,
        ],
        
        'pickup' => [
            'name' => 'Store Pickup',
            'description' => 'Pick up from our store',
            'estimated_days' => 'Same day',
            'tracking' => false,
            'active' => false, // Can be enabled if you have physical stores
            'cost' => 0,
        ],
    ],

    'weight_based' => [
        'enabled' => false,
        'tiers' => [
            ['min_weight' => 0, 'max_weight' => 1, 'additional_cost' => 0],
            ['min_weight' => 1, 'max_weight' => 5, 'additional_cost' => 20],
            ['min_weight' => 5, 'max_weight' => 10, 'additional_cost' => 50],
            ['min_weight' => 10, 'max_weight' => null, 'additional_cost' => 100],
        ],
    ],

    'order_value_based' => [
        'enabled' => true,
        'tiers' => [
            ['min_amount' => 0, 'max_amount' => 500, 'additional_cost' => 0],
            ['min_amount' => 500, 'max_amount' => 1000, 'additional_cost' => 20],
            ['min_amount' => 1000, 'max_amount' => 2000, 'additional_cost' => 10],
            ['min_amount' => 2000, 'max_amount' => null, 'additional_cost' => 0], // Free shipping
        ],
    ],

    'delivery_times' => [
        'standard' => [
            'start_time' => '09:00',
            'end_time' => '18:00',
            'working_days' => [1, 2, 3, 4, 5, 6], // Monday to Saturday
            'exclude_holidays' => true,
        ],
        'express' => [
            'start_time' => '09:00',
            'end_time' => '20:00',
            'working_days' => [1, 2, 3, 4, 5, 6], // Monday to Saturday
            'exclude_holidays' => true,
        ],
    ],

    'packaging' => [
        'default_package_cost' => 10,
        'fragile_handling_cost' => 20,
        'insurance_rate' => 0.01, // 1% of order value
        'insurance_required_threshold' => 5000,
    ],

    'restrictions' => [
        'max_order_value' => 50000,
        'max_weight' => 50, // kg
        'prohibited_items' => [
            'hazardous_materials',
            'perishable_goods',
            'illegal_items',
        ],
    ],

    'notifications' => [
        'customer_notifications' => true,
        'admin_notifications' => true,
        'tracking_updates' => true,
        'delivery_confirmation' => true,
    ],

    'integration' => [
        'default_courier' => 'steadfast',
        'auto_assign_courier' => true,
        'batch_processing' => true,
        'tracking_sync' => true,
    ],
];
