<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Address;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        // Get all users
        $users = User::all();

        foreach ($users as $user) {
            // Create a default shipping address
            Address::create([
                'type' => 'shipping',
                'first_name' => $user->first_name ?? 'Default',
                'last_name' => $user->last_name ?? 'User',
                'phone' => $user->phone ?? '01700000000',
                'email' => $user->email,
                'address_line_1' => 'House #' . rand(1, 999) . ', Road ' . rand(1, 50),
                'address_line_2' => 'Block ' . chr(65 + rand(0, 25)) . ', Sector ' . rand(1, 12),
                'city' => 'Dhaka',
                'postal_code' => rand(1000, 9999),
                'division' => 'Dhaka',
                'country' => 'Bangladesh',
                'is_default' => true,
                'user_id' => $user->id,
            ]);

            // Create a billing address (50% chance)
            if (rand(0, 1)) {
                Address::create([
                    'type' => 'billing',
                    'first_name' => $user->first_name ?? 'Default',
                    'last_name' => $user->last_name ?? 'User',
                    'phone' => $user->phone ?? '01700000000',
                    'email' => $user->email,
                    'address_line_1' => 'Office #' . rand(1, 500) . ', Floor ' . rand(1, 20),
                    'city' => 'Dhaka',
                    'postal_code' => rand(1000, 9999),
                    'division' => 'Dhaka',
                    'country' => 'Bangladesh',
                    'is_default' => false,
                    'user_id' => $user->id,
                ]);
            }
        }
    }
}
