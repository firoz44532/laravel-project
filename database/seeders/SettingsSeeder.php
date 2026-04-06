<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\SettingsService;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        SettingsService::seedDefaults();
    }
}
