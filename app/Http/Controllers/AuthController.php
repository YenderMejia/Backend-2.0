<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ], [
        'email.required' => 'Escribe tu email',
        'email.email' => 'Email no válido',
        'password.required' => 'Escribe tu contraseña',
    ]);

    try {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas',
            ], 401);
        }

        // Crear token de autenticación
        $token = $user->createToken('token')->plainTextToken;

        // Devolver el token junto con el rol del usuario
        return response()->json([
            'token' => $token,
            'role' => $user->role, // Agregar el rol del usuario
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Error en el servidor',
        ], 500);
    }
}


    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json([
            'message' => 'Sesión cerrada',
        ], 200);
    }
}
