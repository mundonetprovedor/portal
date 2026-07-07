<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthenticateClient
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('client_id')) {
            return redirect()->route('login')->with('error', 'Por favor, faça login para acessar.');
        }

        return $next($request);
    }
}
