<?php

namespace App\Services;

use App\Services\SteadfastCourierService;
use App\Services\PathaoCourierService;
use App\Services\eCourierService;
use App\Services\RedXService;

class CourierFactory
{
    /**
     * Get courier service instance
     */
    public static function get($courierType)
    {
        switch (strtolower($courierType)) {
            case 'steadfast':
                return app(SteadfastCourierService::class);
            case 'pathao':
                return app(PathaoCourierService::class);
            case 'ecourier':
                return app(eCourierService::class);
            case 'redx':
                return app(RedXService::class);
            default:
                throw new \InvalidArgumentException("Unsupported courier type: {$courierType}");
        }
    }

    /**
     * Get all available couriers
     */
    public static function getAvailableCouriers()
    {
        return [
            'steadfast' => [
                'name' => 'Steadfast Courier',
                'description' => 'Fast and reliable delivery service',
                'icon' => 'fas fa-truck',
                'color' => 'orange',
                'api_available' => true,
                'coverage' => 'Nationwide',
                'cod_support' => true,
                'special_features' => ['Real-time tracking', 'Same day delivery', 'SMS notifications']
            ],
            'pathao' => [
                'name' => 'Pathao Courier',
                'description' => 'Quick delivery with real-time tracking',
                'icon' => 'fas fa-bicycle',
                'color' => 'green',
                'api_available' => true,
                'coverage' => '64 Districts',
                'cod_support' => true,
                'special_features' => ['Mobile app tracking', 'Multiple delivery options', 'Express delivery']
            ],
            'ecourier' => [
                'name' => 'eCourier',
                'description' => 'Professional e-commerce delivery solution',
                'icon' => 'fas fa-shipping-fast',
                'color' => 'blue',
                'api_available' => true,
                'coverage' => 'Nationwide',
                'cod_support' => true,
                'special_features' => ['Advanced tracking', 'Delivery analytics', 'Multiple payment options']
            ],
            'redx' => [
                'name' => 'RedX',
                'description' => 'E-commerce friendly delivery service',
                'icon' => 'fas fa-box',
                'color' => 'red',
                'api_available' => true,
                'coverage' => '64 Districts',
                'cod_support' => true,
                'special_features' => ['Dashboard integration', 'Bulk processing', 'Smart routing']
            ]
        ];
    }

    /**
     * Get courier by city/area
     */
    public static function getCouriersByArea($city)
    {
        $allCouriers = self::getAvailableCouriers();
        
        // Logic to determine which couriers are available in specific areas
        // This can be enhanced with actual coverage data
        $majorCities = ['dhaka', 'chattogram', 'sylhet', 'khulna', 'rajshahi', 'barishal', 'rangpur', 'mymensingh'];
        
        if (in_array(strtolower($city), $majorCities)) {
            return $allCouriers; // All couriers available in major cities
        }
        
        // For other areas, return couriers with nationwide coverage
        return array_filter($allCouriers, function($courier) {
            return $courier['coverage'] === 'Nationwide';
        });
    }

    /**
     * Get best courier based on order criteria
     */
    public static function getBestCourier($order, $criteria = ['speed', 'cost', 'reliability'])
    {
        $couriers = self::getAvailableCouriers();
        $scores = [];

        foreach ($couriers as $type => $courier) {
            $score = 0;
            
            // Speed scoring
            if (in_array('speed', $criteria)) {
                if ($type === 'pathao') $score += 3; // Pathao is fastest
                elseif ($type === 'steadfast') $score += 2;
                elseif ($type === 'ecourier') $score += 2;
                elseif ($type === 'redx') $score += 1;
            }
            
            // Cost scoring (lower is better, so we invert)
            if (in_array('cost', $criteria)) {
                if ($type === 'redx') $score += 3; // Usually cheaper
                elseif ($type === 'pathao') $score += 2;
                elseif ($type === 'steadfast') $score += 1;
                elseif ($type === 'ecourier') $score += 1;
            }
            
            // Reliability scoring
            if (in_array('reliability', $criteria)) {
                if ($type === 'steadfast') $score += 3; // Most reliable
                elseif ($type === 'ecourier') $score += 3;
                elseif ($type === 'redx') $score += 2;
                elseif ($type === 'pathao') $score += 2;
            }
            
            $scores[$type] = $score;
        }
        
        // Return courier with highest score
        $bestType = array_keys($scores, max($scores))[0];
        return $couriers[$bestType];
    }

    /**
     * Validate courier configuration
     */
    public static function validateCourierConfig($courierType)
    {
        $requiredConfigs = [
            'steadfast' => ['STEADFAST_API_KEY', 'STEADFAST_SECRET_KEY'],
            'pathao' => ['PATHAO_CLIENT_EMAIL', 'PATHAO_CLIENT_PASSWORD', 'PATHAO_CLIENT_SECRET'],
            'ecourier' => ['ECOURIER_API_KEY', 'ECOURIER_SECRET_KEY', 'ECOURIER_USER_ID'],
            'redx' => ['REDX_API_KEY', 'REDX_STORE_ID']
        ];

        if (!isset($requiredConfigs[$courierType])) {
            return ['valid' => false, 'message' => 'Unknown courier type'];
        }

        $missing = [];
        foreach ($requiredConfigs[$courierType] as $config) {
            if (empty(env($config))) {
                $missing[] = $config;
            }
        }

        if (empty($missing)) {
            return ['valid' => true, 'message' => 'Configuration is valid'];
        }

        return [
            'valid' => false, 
            'message' => 'Missing configuration: ' . implode(', ', $missing)
        ];
    }

    /**
     * Get courier statistics
     */
    public static function getCourierStats()
    {
        // This would typically fetch from database
        // For now, return mock data
        return [
            'steadfast' => [
                'total_orders' => 1250,
                'success_rate' => 98.5,
                'avg_delivery_time' => '1.5 days',
                'customer_rating' => 4.7
            ],
            'pathao' => [
                'total_orders' => 980,
                'success_rate' => 97.2,
                'avg_delivery_time' => '1.2 days',
                'customer_rating' => 4.6
            ],
            'ecourier' => [
                'total_orders' => 750,
                'success_rate' => 99.1,
                'avg_delivery_time' => '2.0 days',
                'customer_rating' => 4.8
            ],
            'redx' => [
                'total_orders' => 620,
                'success_rate' => 96.8,
                'avg_delivery_time' => '1.8 days',
                'customer_rating' => 4.5
            ]
        ];
    }
}
