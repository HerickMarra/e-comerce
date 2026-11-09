<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('position')->orderBy('created_at', 'desc')->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'media_type' => 'required|in:video,image',
            'video_url' => 'nullable|string|max:1000',
            'video_file' => 'nullable|file|mimes:mp4,webm,ogg,mov|max:102400', // max 100MB
            'image_url' => 'nullable|string|max:1000',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,avif|max:10240', // max 10MB
            'btn_text' => 'nullable|string|max:100',
            'btn_link' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->only(['title', 'media_type', 'video_url', 'image_url', 'btn_text', 'btn_link']);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['btn_text'] = $data['btn_text'] ?: 'Ver Catálogo';
        $data['btn_link'] = $data['btn_link'] ?: '/categorias';

        if ($request->hasFile('video_file') && $request->file('video_file')->isValid()) {
            $data['video_file'] = $request->file('video_file')->store('banners/videos', 'public');
        }

        if ($request->hasFile('image_file') && $request->file('image_file')->isValid()) {
            $data['image_file'] = $request->file('image_file')->store('banners/images', 'public');
        }

        Banner::create($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner criado com sucesso!');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'media_type' => 'required|in:video,image',
            'video_url' => 'nullable|string|max:1000',
            'video_file' => 'nullable|file|mimes:mp4,webm,ogg,mov|max:102400',
            'image_url' => 'nullable|string|max:1000',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,avif|max:10240',
            'btn_text' => 'nullable|string|max:100',
            'btn_link' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->only(['title', 'media_type', 'video_url', 'image_url', 'btn_text', 'btn_link']);
        $data['is_active'] = $request->boolean('is_active', false);
        $data['btn_text'] = $data['btn_text'] ?: 'Ver Catálogo';
        $data['btn_link'] = $data['btn_link'] ?: '/categorias';

        if ($request->boolean('remove_video_file')) {
            if ($banner->video_file) {
                Storage::disk('public')->delete($banner->video_file);
            }
            $data['video_file'] = null;
        } elseif ($request->hasFile('video_file') && $request->file('video_file')->isValid()) {
            if ($banner->video_file) {
                Storage::disk('public')->delete($banner->video_file);
            }
            $data['video_file'] = $request->file('video_file')->store('banners/videos', 'public');
        }

        if ($request->boolean('remove_image_file')) {
            if ($banner->image_file) {
                Storage::disk('public')->delete($banner->image_file);
            }
            $data['image_file'] = null;
        } elseif ($request->hasFile('image_file') && $request->file('image_file')->isValid()) {
            if ($banner->image_file) {
                Storage::disk('public')->delete($banner->image_file);
            }
            $data['image_file'] = $request->file('image_file')->store('banners/images', 'public');
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner atualizado com sucesso!');
    }

    public function toggleStatus(Banner $banner)
    {
        $banner->update(['is_active' => !$banner->is_active]);

        return back()->with('success', 'Status do banner alterado com sucesso!');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->video_file) {
            Storage::disk('public')->delete($banner->video_file);
        }
        if ($banner->image_file) {
            Storage::disk('public')->delete($banner->image_file);
        }

        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Banner removido com sucesso!');
    }
}

