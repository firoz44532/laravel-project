<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Category;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share categories with all views
        View::composer(['frontend.*'], function ($view) {
            $view->with('categories', $this->getMainCategories());
        });

        // Share categories with auth views
        View::composer(['frontend.auth.*'], function ($view) {
            $view->with('categories', $this->getMainCategories());
        });
    }

    /**
     * Get main categories for navigation
     */
    private function getMainCategories()
    {
        return Category::where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();
    }
}
