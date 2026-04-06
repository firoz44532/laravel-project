<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class CommissionSettingsSeeder extends Seeder
{
    public function run()
    {
        $commissionSettings = [
            [
                'key' => 'commission_admin_percentage',
                'title' => 'Admin Commission Percentage',
                'description' => 'Percentage commission taken by admin from each sale',
                'type' => 'number',
                'group' => 'commission',
                'value' => '5',
                'is_public' => false,
                'sort_order' => 1,
            ],
            [
                'key' => 'commission_merchant_percentage',
                'title' => 'Merchant Commission Percentage',
                'description' => 'Percentage commission given to merchants from each sale',
                'type' => 'number',
                'group' => 'commission',
                'value' => '10',
                'is_public' => false,
                'sort_order' => 2,
            ],
            [
                'key' => 'commission_affiliate_percentage',
                'title' => 'Affiliate Commission Percentage',
                'description' => 'Percentage commission given to affiliates from each sale',
                'type' => 'number',
                'group' => 'commission',
                'value' => '3',
                'is_public' => false,
                'sort_order' => 3,
            ],
            [
                'key' => 'commission_enabled',
                'title' => 'Enable Commission System',
                'description' => 'Enable or disable the commission system',
                'type' => 'boolean',
                'group' => 'commission',
                'value' => '1',
                'is_public' => false,
                'sort_order' => 0,
            ],
            [
                'key' => 'commission_minimum_payout',
                'title' => 'Minimum Payout Amount',
                'description' => 'Minimum amount required for commission payout',
                'type' => 'number',
                'group' => 'commission',
                'value' => '50',
                'is_public' => false,
                'sort_order' => 4,
            ],
            [
                'key' => 'commission_payment_method',
                'title' => 'Commission Payment Method',
                'description' => 'Default payment method for commission payouts',
                'type' => 'text',
                'group' => 'commission',
                'value' => 'bank_transfer',
                'is_public' => false,
                'sort_order' => 5,
            ],
            [
                'key' => 'commission_calculation_method',
                'title' => 'Commission Calculation Method',
                'description' => 'Method for calculating commission (percentage or fixed)',
                'type' => 'text',
                'group' => 'commission',
                'value' => 'percentage',
                'is_public' => false,
                'sort_order' => 6,
            ],
            [
                'key' => 'commission_withholding_tax',
                'title' => 'Withholding Tax Percentage',
                'description' => 'Tax percentage to withhold from commission payments',
                'type' => 'number',
                'group' => 'commission',
                'value' => '0',
                'is_public' => false,
                'sort_order' => 7,
            ],
        ];

        foreach ($commissionSettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
