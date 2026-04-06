<?php

namespace App\Providers;

use App\Services\SuspiciousCustomerDetectionService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SuspiciousCustomerDetectionService::class, function ($app) {
            return new SuspiciousCustomerDetectionService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
