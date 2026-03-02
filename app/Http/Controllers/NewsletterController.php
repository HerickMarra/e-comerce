<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $exists = Newsletter::where('email', $request->email)->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Este e-mail já está inscrito em nossa newsletter.'
            ], 422);
        }

        Newsletter::create([
            'email' => $request->email
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Inscrição realizada com sucesso! Obrigado por nos acompanhar.'
        ]);
    }
}
