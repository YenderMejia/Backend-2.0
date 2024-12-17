<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomRedirectIfAuthenticated extends RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$guards) :Response
    {
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Si la solicitud es JSON, devolver un 403
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'No autorizado',
                    ], 403);
                }

                // De lo contrario, redirigir al dashboard o ruta principal
                return $this->redirectTo($request);
            }
        }

        return $next($request);
    }
}
