<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::with(['categories', 'images'])
            ->where('is_active', true)
            ->latest()
            ->take(4)
            ->get();

        $categories = Category::withCount('products')
            ->orderBy('products_count', 'desc')
            ->take(2)
            ->get();

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

        // Configurações da Hero Section
        $heroSettings = [
            'tag' => \App\Models\Setting::get('home_hero_tag', 'Coleção 2024'),
            'title' => \App\Models\Setting::get('home_hero_title', 'Redefina a Elegância do seu Lar'),
            'subtitle' => \App\Models\Setting::get('home_hero_subtitle', 'Descubra o luxo do design minimalista e a maestria artesanal. Peças exclusivas concebidas para o viver contemporâneo.'),
            'btn_text' => \App\Models\Setting::get('home_hero_btn_text', 'Ver Catálogo'),
            'btn_link' => \App\Models\Setting::get('home_hero_btn_link', route('categories.index')),
            'video_url' => $this->formatYoutubeUrl(\App\Models\Setting::get('home_hero_video_url', 'https://www.youtube.com/watch?v=uFZRqTRk8-o')),
            'image_fallback' => \App\Models\Setting::get('home_hero_image_fallback', 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&q=80&w=2070'),
        ];

        return view('welcome', compact('products', 'categories', 'luckyProducts', 'randomSections', 'heroSettings'));
    }

    private function formatYoutubeUrl($url)
    {
        if (empty($url))
            return '';

        // If it's already an embed URL, just ensure parameters are right if possible, or return as is
        if (str_contains($url, '/embed/')) {
            // Ensure it has background parameters if not present
            if (!str_contains($url, 'autoplay=')) {
                $separator = str_contains($url, '?') ? '&' : '?';
                $url .= $separator . 'autoplay=1&mute=1&controls=0&loop=1&showinfo=0&rel=0&disablekb=1&iv_load_policy=3&modestbranding=1&fs=0&playsinline=1';
            }
            return $url;
        }

        $videoId = null;

        // Handle youtube.com/watch?v=ID or youtu.be/ID
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^& \? \n]+)/', $url, $matches)) {
            $videoId = $matches[1];
        }

        if ($videoId) {
            return "https://www.youtube.com/embed/{$videoId}?autoplay=1&mute=1&controls=0&loop=1&playlist={$videoId}&showinfo=0&rel=0&disablekb=1&iv_load_policy=3&modestbranding=1&fs=0&playsinline=1";
        }

        return $url;
    }
}