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

        $categories = Category::all();

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
            'video_url' => \App\Models\Setting::get('home_hero_video_url', 'https://www.youtube.com/embed/uFZRqTRk8-o?autoplay=1&mute=1&controls=0&loop=1&playlist=uFZRqTRk8-o&showinfo=0&rel=0&disablekb=1&iv_load_policy=3&modestbranding=1&fs=0&playsinline=1'),
            'image_fallback' => \App\Models\Setting::get('home_hero_image_fallback', 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&q=80&w=2070'),
        ];

        return view('welcome', compact('products', 'categories', 'luckyProducts', 'randomSections', 'heroSettings'));
    }
}