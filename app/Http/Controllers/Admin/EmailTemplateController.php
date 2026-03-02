<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::latest()->paginate(10);
        return view('admin.emails.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.emails.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:system,marketing',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Ensure slug is unique
        $count = EmailTemplate::where('slug', 'like', $validated['slug'] . '%')->count();
        if ($count > 0) {
            $validated['slug'] .= '-' . ($count + 1);
        }

        EmailTemplate::create($validated);

        return redirect()->route('admin.emails.index')->with('success', 'Modelo de e-mail criado com sucesso!');
    }

    public function edit(EmailTemplate $email)
    {
        return view('admin.emails.edit', compact('email'));
    }

    public function update(Request $request, EmailTemplate $email)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // If it's a system template, we only allow editing name, subject and content.
        // Type and is_system should not be changed via UI for system templates if we want to be safe.

        $email->update($validated);

        return redirect()->route('admin.emails.index')->with('success', 'Modelo de e-mail atualizado com sucesso!');
    }

    public function destroy(EmailTemplate $email)
    {
        if ($email->is_system) {
            return redirect()->route('admin.emails.index')->with('error', 'Modelos de e-mail do sistema não podem ser excluídos!');
        }

        $email->delete();

        return redirect()->route('admin.emails.index')->with('success', 'Modelo de e-mail excluído com sucesso!');
    }
}
