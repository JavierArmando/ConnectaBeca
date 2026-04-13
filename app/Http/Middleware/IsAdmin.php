<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Verificar si el usuario está autenticado
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Verificar si el usuario es admin
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('index')->with('error', 'No tienes permisos para acceder a esta página');
        }

        return $next($request);
    }
}
