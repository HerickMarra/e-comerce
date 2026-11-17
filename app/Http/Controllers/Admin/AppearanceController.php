<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppearanceController extends Controller
{
    public function index()
    {
        $settings = [
            'home_hero_tag' => Setting::get('home_hero_tag', 'Coleção 2026'),
            'home_hero_title' => Setting::get('home_hero_title', 'Sua Próxima Missão Começa Aqui'),
            'home_hero_subtitle' => Setting::get('home_hero_subtitle', 'Equipamentos táticos, armas de airsoft, pescaria e artigos de camping com alta durabilidade e tecnologia.'),
            'home_hero_btn_text' => Setting::get('home_hero_btn_text', 'Ver Catálogo'),
            'home_hero_btn_link' => Setting::get('home_hero_btn_link', '/explorar'),
            'home_hero_video_url' => Setting::get('home_hero_video_url', 'https://www.youtube.com/embed/uFZRqTRk8-o?autoplay=1&mute=1&controls=0&loop=1&playlist=uFZRqTRk8-o&showinfo=0&rel=0&disablekb=1&iv_load_policy=3&modestbranding=1&fs=0&playsinline=1'),
            'home_hero_image_fallback' => Setting::get('home_hero_image_fallback', 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&q=80&w=2070'),
            'auth_login_image' => Setting::get('auth_login_image'),
            'auth_login_title' => Setting::get('auth_login_title', 'Sua próxima missão começa aqui.'),
            'auth_login_subtitle' => Setting::get('auth_login_subtitle', 'Equipamentos de airsoft, pescaria, barracas e vestuário tático.'),
            'auth_login_badge' => Setting::get('auth_login_badge', 'Acesso Exclusivo'),
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
            'auth_login_title' => 'nullable|string|max:255',
            'auth_login_subtitle' => 'nullable|string',
            'auth_login_badge' => 'nullable|string|max:100',
            'auth_login_image_url' => 'nullable|string',
            'auth_login_image' => 'nullable|image|max:10240',
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
            if ($request->has($key)) {
                Setting::set($key, $request->input($key));
            }
        }

        // Login Screen Presentation & Image
        if ($request->has('auth_login_title')) {
            Setting::set('auth_login_title', $request->auth_login_title);
        }
        if ($request->has('auth_login_subtitle')) {
            Setting::set('auth_login_subtitle', $request->auth_login_subtitle);
        }
        if ($request->has('auth_login_badge')) {
            Setting::set('auth_login_badge', $request->auth_login_badge);
        }

        if ($request->remove_auth_login_image === '1') {
            $oldImage = Setting::get('auth_login_image');
            if ($oldImage && !str_starts_with($oldImage, 'http')) {
                Storage::disk('public')->delete($oldImage);
            }
            Setting::set('auth_login_image', null);
        } elseif ($request->hasFile('auth_login_image') && $request->file('auth_login_image')->isValid()) {
            $oldImage = Setting::get('auth_login_image');
            if ($oldImage && !str_starts_with($oldImage, 'http')) {
                Storage::disk('public')->delete($oldImage);
            }
            $path = $request->file('auth_login_image')->store('auth', 'public');
            Setting::set('auth_login_image', $path);
        } elseif ($request->filled('auth_login_image_url')) {
            $oldImage = Setting::get('auth_login_image');
            if ($oldImage && !str_starts_with($oldImage, 'http')) {
                Storage::disk('public')->delete($oldImage);
            }
            Setting::set('auth_login_image', trim($request->auth_login_image_url));
        }

        return redirect()->route('admin.appearance.index')->with('success', 'Configurações de aparência atualizadas com sucesso!');
    }
}
