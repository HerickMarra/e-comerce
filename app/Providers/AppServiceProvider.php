<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Share branding settings lazily with Blade views
        View::composer('*', function ($view) {
            $view->with('appSettings', [
                'store_name' => Setting::get('store_name', config('app.name')),
                'store_tagline' => Setting::get('store_tagline', 'Móveis de alto padrão para sua casa'),
                'store_logo' => Setting::get('store_logo'),
                'store_logo_size' => Setting::get('store_logo_size', '100'),
                'store_icon' => Setting::get('store_icon', 'chair'),
                'primary_color' => Setting::get('primary_color', '#10b981'),
                'secondary_color' => Setting::get('secondary_color', '#0f172a'),
            ]);
        });

        // Share top 3 categories by product count
        View::composer('components.storefront-layout', function ($view) {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('categories')) {
                    $topCategories = \App\Models\Category::withCount('products')
                        ->orderBy('products_count', 'desc')
                        ->take(3)
                        ->get();
                    $view->with('navCategories', $topCategories);
                } else {
                    $view->with('navCategories', collect());
                }
            } catch (\Throwable $e) {
                $view->with('navCategories', collect());
            }
        });
    }
}
