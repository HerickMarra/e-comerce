<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->get();

        // Produtos Aleatórios ("Estou com Sorte")
        $luckyProducts = Product::with(['categories', 'images'])
            ->where('is_active', true)
            ->inRandomOrder()
            ->take(4)
            ->get();

        // Categorias Aleatórias (Seções Extras)
        $randomSections = Category::whereHas('products', function ($query) {
            $query->where('is_active', true);
        })
            ->inRandomOrder()
            ->take(2)
            ->get()
            ->map(function ($category) {
                // Carregar 4 produtos para esta categoria
                $category->section_products = $category->products()
                    ->with(['images', 'categories'])
                    ->where('is_active', true)
                    ->inRandomOrder()
                    ->take(4)
                    ->get();
                return $category;
            });

        // Obter banner aleatório do módulo de Banners
        $randomBanner = \App\Models\Banner::where('is_active', true)->inRandomOrder()->first();

        $rawVideoUrl = $randomBanner && $randomBanner->isVideo() 
            ? ($randomBanner->video_url ?: '') 
            : \App\Models\Setting::get('home_hero_video_url', 'https://www.youtube.com/watch?v=HKiTf7EjKtg');

        $youtubeId = \App\Models\Banner::extractYoutubeId($rawVideoUrl);

        // Configurações da Hero Section
        $heroSettings = [
            'tag' => \App\Models\Setting::get('home_hero_tag', 'Coleção'),
            'title' => $randomBanner?->title ?: \App\Models\Setting::get('home_hero_title', 'Redefina a Elegância do seu Lar'),
            'subtitle' => \App\Models\Setting::get('home_hero_subtitle', ''),
            'btn_text' => $randomBanner?->btn_text ?: \App\Models\Setting::get('home_hero_btn_text', 'Ver Catálogo'),
            'btn_link' => $randomBanner?->btn_link ?: \App\Models\Setting::get('home_hero_btn_link', route('categories.index')),
            'media_type' => $randomBanner ? $randomBanner->media_type : 'video',
            'is_direct_video' => $randomBanner ? $randomBanner->isDirectVideo() : false,
            'video_url' => $randomBanner && $randomBanner->isVideo() 
                ? $randomBanner->effective_video_url 
                : (!$randomBanner ? \App\Models\Banner::formatYoutubeUrl(\App\Models\Setting::get('home_hero_video_url', 'https://www.youtube.com/watch?v=HKiTf7EjKtg')) : ''),
            'youtube_id' => $youtubeId,
            'image_fallback' => $randomBanner && $randomBanner->effective_image_url 
                ? $randomBanner->effective_image_url 
                : \App\Models\Setting::get('home_hero_image_fallback', 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&q=80&w=2070'),
            'banner_id' => $randomBanner?->id,
        ];

        return view('welcome', compact('categories', 'luckyProducts', 'randomSections', 'heroSettings'));
    }

    public function family()
    {
        return view('pages.family');
    }

    private function formatYoutubeUrl($url)
    {
        return \App\Models\Banner::formatYoutubeUrl($url);
    }
}