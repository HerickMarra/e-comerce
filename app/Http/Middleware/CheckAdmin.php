<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || (!auth()->user()->hasRole('Super Admin') && !auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Manager'))) {
            abort(403, 'Acesso negado. Você não tem permissão para acessar esta área.');
        }

        return $next($request);
    }
}
