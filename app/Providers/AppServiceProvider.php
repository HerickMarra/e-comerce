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
        // Share branding settings globally with all Blade views.
        // Setting::get() reads from a single in-memory-cached key -- zero extra DB queries.
        View::share('appSettings', [
            'store_name' => Setting::get('store_name', config('app.name')),
            'store_tagline' => Setting::get('store_tagline', 'Móveis de alto padrão para sua casa'),
            'store_logo' => Setting::get('store_logo'),
            'store_logo_size' => Setting::get('store_logo_size', '100'),
            'store_icon' => Setting::get('store_icon', 'chair'),
            'primary_color' => Setting::get('primary_color', '#10b981'),
            'secondary_color' => Setting::get('secondary_color', '#0f172a'),
        ]);

        // Share top 4 categories by product count
        View::composer('components.storefront-layout', function ($view) {
            $topCategories = \App\Models\Category::withCount('products')
                ->orderBy('products_count', 'desc')
                ->take(4)
                ->get();
            $view->with('navCategories', $topCategories);
        });
    }
}
