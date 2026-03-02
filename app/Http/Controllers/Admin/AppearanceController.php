<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class AppearanceController extends Controller
{
    public function index()
    {
        $settings = [
            'home_hero_tag' => Setting::get('home_hero_tag', 'Coleção 2024'),
            'home_hero_title' => Setting::get('home_hero_title', 'Redefina a Elegância do seu Lar'),
            'home_hero_subtitle' => Setting::get('home_hero_subtitle', 'Descubra o luxo do design minimalista e a maestria artesanal. Peças exclusivas concebidas para o viver contemporâneo.'),
            'home_hero_btn_text' => Setting::get('home_hero_btn_text', 'Ver Catálogo'),
            'home_hero_btn_link' => Setting::get('home_hero_btn_link', '/categorias'),
            'home_hero_video_url' => Setting::get('home_hero_video_url', 'https://www.youtube.com/embed/uFZRqTRk8-o?autoplay=1&mute=1&controls=0&loop=1&playlist=uFZRqTRk8-o&showinfo=0&rel=0&disablekb=1&iv_load_policy=3&modestbranding=1&fs=0&playsinline=1'),
            'home_hero_image_fallback' => Setting::get('home_hero_image_fallback', 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&q=80&w=2070'),
        ];

        return view('admin.appearance.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'home_hero_tag' => 'sometimes|string|max:255',
            'home_hero_title' => 'sometimes|string|max:255',
            'home_hero_subtitle' => 'sometimes|string',
            'home_hero_btn_text' => 'sometimes|string|max:255',
            'home_hero_btn_link' => 'sometimes|string|max:255',
            'home_hero_video_url' => 'sometimes|string',
            'home_hero_image_fallback' => 'sometimes|string',
        ]);

        $keys = [
            'home_hero_tag',
            'home_hero_title',
            'home_hero_subtitle',
            'home_hero_btn_text',
            'home_hero_btn_link',
            'home_hero_video_url',
            'home_hero_image_fallback',
        ];

        foreach ($keys as $key) {
            Setting::set($key, $request->input($key));
        }

        return redirect()->route('admin.appearance.index')->with('success', 'Configurações de aparência atualizadas com sucesso!');
    }
}
