<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AllowGuestAccess
{
    /**
     * Handle an incoming request.
     * Allow if user is authenticated or has chosen to continue as guest.
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            return $next($request);
        }

        if ($request->session()->get('guest', false)) {
            return $next($request);
        }

        return redirect()->guest(route('login'));
    }
}
