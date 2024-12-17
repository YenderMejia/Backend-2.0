<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{

    /**
     * Muestra una lista de todos los usuarios que sean administradores o empleados.
     */
    public function index()
    {
        // Filtra usuarios que sean administradores o empleados
        $users = User::whereIn('role', ['admin', 'empleado'])->get();

        return response()->json($users);
    }

    /**
     * Registra un nuevo usuario.
     */
    public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => ['required', Rule::in(['admin', 'empleado'])],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return response()->json(['message' => 'Usuario creado con éxito', 'user' => $user], 201);

    } catch (\Exception $e) {
        // Retornar el error exacto
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


    /**
     * Muestra un usuario específico si es administrador o empleado.
     */
    public function show($id)
    {
        // Busca el usuario por ID y verifica que sea administrador o empleado
        $user = User::where('id', $id)->whereIn('role', ['admin', 'empleado'])->first();

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado o no autorizado'], 404);
        }

        return response()->json($user);
    }

    /**
     * Actualiza un usuario existente.
     */
    public function update(Request $request, $id)
    {
        // Busca el usuario por ID y verifica que sea administrador o empleado
        $user = User::where('id', $id)->whereIn('role', ['admin', 'empleado'])->first();

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado o no autorizado'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:8|confirmed',
            'role' => ['sometimes', Rule::in(['admin', 'empleado'])],
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json(['message' => 'Usuario actualizado con éxito', 'user' => $user]);
    }

    /**
     * Elimina un usuario si es administrador o empleado.
     */
    public function destroy($id)
    {
        // Busca el usuario por ID y verifica que sea administrador o empleado
        $user = User::where('id', $id)->whereIn('role', ['admin', 'empleado'])->first();

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado o no autorizado'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'Usuario eliminado con éxito']);
    }
}
