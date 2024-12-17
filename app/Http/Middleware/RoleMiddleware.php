<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, $roles): Response
    {
        // Verifica si el usuario está autenticado
        if (!$request->user()) {
            return response()->json([
                'message' => 'No autenticado. Por favor, inicie sesión.',
            ], 401);
        }

        // Convierte los roles permitidos en un array
        $rolesArray = explode('|', $roles);

        // Verifica si el rol del usuario está en la lista de roles permitidos
        if (!in_array($request->user()->role, $rolesArray)) {
            return response()->json([
                'message' => 'Acceso denegado: No tiene permisos para esta acción.',
                'user_role' => $request->user()->role, // Muestra el rol del usuario (opcional para depuración)
                'allowed_roles' => $rolesArray,        // Muestra los roles permitidos (opcional para depuración)
            ], 403);
        }

        // Continúa con la solicitud
        return $next($request);
    }
}
