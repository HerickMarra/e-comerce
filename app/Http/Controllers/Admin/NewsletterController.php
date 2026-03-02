<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function index()
    {
        $subscriptions = Newsletter::latest()->paginate(20);
        return view('admin.newsletters.index', compact('subscriptions'));
    }

    public function destroy(Newsletter $newsletter)
    {
        $newsletter->delete();
        return redirect()->route('admin.newsletters.index')->with('success', 'Inscrição removida com sucesso.');
    }
}
