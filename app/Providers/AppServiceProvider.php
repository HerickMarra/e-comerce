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
        // Share branding and public store settings with Blade views
        View::composer('*', function ($view) {
            $view->with('appSettings', [
                'store_name' => Setting::get('store_name', config('app.name', 'Denner Tênis')),
                'store_company_name' => Setting::get('store_company_name', ''),
                'store_tagline' => Setting::get('store_tagline', 'Móveis de alto padrão para sua casa'),
                'store_logo' => Setting::get('store_logo'),
                'store_logo_size' => Setting::get('store_logo_size', '100'),
                'store_icon' => Setting::get('store_icon', 'chair'),
                'primary_color' => Setting::get('primary_color', '#10b981'),
                'secondary_color' => Setting::get('secondary_color', '#0f172a'),
                'store_document' => Setting::get('store_document', ''),
                'store_state_registration' => Setting::get('store_state_registration', ''),
                'store_phone' => Setting::get('store_phone', ''),
                'store_whatsapp' => Setting::get('store_whatsapp', Setting::get('store_phone', '')),
                'store_whatsapp_message' => Setting::get('store_whatsapp_message', 'Olá! Gostaria de mais informações sobre os produtos.'),
                'store_email' => Setting::get('store_email', Setting::get('mail_from_address', '')),
                'store_opening_hours' => Setting::get('store_opening_hours', 'Segunda a Sexta: 09h às 18h | Sábado: 09h às 13h'),
                'store_street' => Setting::get('store_street', ''),
                'store_number' => Setting::get('store_number', ''),
                'store_complement' => Setting::get('store_complement', ''),
                'store_neighborhood' => Setting::get('store_neighborhood', ''),
                'store_city' => Setting::get('store_city', ''),
                'store_state' => Setting::get('store_state', ''),
                'store_zip' => Setting::get('store_zip', ''),
                'store_instagram' => Setting::get('store_instagram', ''),
                'store_facebook' => Setting::get('store_facebook', ''),
                'store_tiktok' => Setting::get('store_tiktok', ''),
                'store_youtube' => Setting::get('store_youtube', ''),
                'store_footer_description' => Setting::get('store_footer_description', 'Sua loja especializada em tênis de corrida, treino e performance das melhores marcas mundiais, com envio rápido e garantia de originalidade.'),
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
