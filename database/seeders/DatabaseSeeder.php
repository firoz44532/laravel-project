<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SettingsSeeder::class,
            CategorySeeder::class,
            BannerSeeder::class,
            ProductSeeder::class,
            ReviewSeeder::class,
            ShippingSeeder::class,
        ]);

        // Create admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@shop.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        // Create regular user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@shop.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
    }
}
