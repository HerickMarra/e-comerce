<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->latest()->paginate(20);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
        ]);

        $data = [
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
        ];

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $data['image_path'] = $request->file('image')->store('categories', 'public');
        }

        Category::create($data);

        return redirect()->route('admin.categories.index')
            ->with('success', "Categoria \"{$validated['name']}\" criada com sucesso!");
    }

    public function edit(Category $category)
    {
        $category->loadCount('products');
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $data = [
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
        ];

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Delete old image
            if ($category->image_path) {
                Storage::disk('public')->delete($category->image_path);
            }
            $data['image_path'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return redirect()->route('admin.categories.index')
            ->with('success', "Categoria \"{$validated['name']}\" atualizada com sucesso!");
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return back()->withErrors(['message' => "A categoria \"{$category->name}\" possui produtos vinculados e não pode ser removida."]);
        }

        $name = $category->name;

        // Delete image
        if ($category->image_path) {
            Storage::disk('public')->delete($category->image_path);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', "Categoria \"{$name}\" removida.");
    }
}
