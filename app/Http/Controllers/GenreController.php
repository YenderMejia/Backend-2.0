<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    /**
     *  Obtiene todos los géneros.
     */
    public function index()
    {
        $genres = Genre::all();
        return response()->json($genres);
    }

    /**
     * Crea un género.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:genres',
        ]);

        $genre = Genre::create($validated);

        return response()->json($genre, 201);
    }

    /**
     * Obtiene un género.
     */
    public function show($id)
    {
        $genre = Genre::find($id);

        if (!$genre) {
            return response()->json(['message' => 'Genero no encontrado'], 404);
        }

        return response()->json($genre);
    }

    /**
     * Actualiza un género.
     */
    public function update(Request $request, $id)
    {
        $genre = Genre::find($id);

        if (!$genre) {
            return response()->json(['message' => 'Genero no encontrado'], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:genres,name,' . $id,
        ]);

        $genre->update($validated);

        return response()->json($genre);
    }

    /**
     * Elimina un género.
     */
    public function destroy($id)
    {
        $genre = Genre::find($id);

        if (!$genre) {
            return response()->json(['message' => 'Genero no encontrado'], 404);
        }

        $genre->delete();

        return response()->json(['message' => 'Genero eliminado']);
    }
}
