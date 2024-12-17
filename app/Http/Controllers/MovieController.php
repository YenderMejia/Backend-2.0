<?php


namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MovieController extends Controller
{
    private $baseURL;
    private $imageBaseURL;
    private $apiKey;

    public function __construct()
    {
        // Inicializar las URLs y la clave de API desde el archivo .env
        $this->baseURL = env('MOVIE_DB_BASE_URL');  // URL base de la API
        $this->imageBaseURL = env('MOVIE_DB_IMAGE_BASE_URL');  // URL base para imágenes
        $this->apiKey = env('MOVIE_DB_API_KEY');  // Clave de API
    }

    /**
     * Obtener películas populares
     */
    public function getPopularMovies()
    {
        // Petición HTTP a la API de The Movie DB
        $response = Http::get("{$this->baseURL}/movie/popular", [
            'api_key' => $this->apiKey,
            'language' => 'es-ES',  // Lenguaje de los resultados
        ]);

        $movies = $response->json();  // Obtener la respuesta en formato JSON

        // Agregar URLs completas de imágenes
        foreach ($movies['results'] as &$movie) {
            $movie['poster_full_path'] = "{$this->imageBaseURL}/w500" . $movie['poster_path'];
            $movie['backdrop_full_path'] = "{$this->imageBaseURL}/w780" . $movie['backdrop_path'];
        }

        return response()->json($movies);  // Devolver la respuesta como JSON
    }

    /**
     * Obtener detalles de una película por su ID
     */
    public function getMovieDetails($id)
    {
        // Petición HTTP para obtener los detalles de una película
        $response = Http::get("{$this->baseURL}/movie/{$id}", [
            'api_key' => $this->apiKey,
            'language' => 'es-ES',  // Lenguaje de los detalles
        ]);

        $movie = $response->json();  // Obtener la respuesta en formato JSON

        // Agregar URLs completas de imágenes
        $movie['poster_full_path'] = "{$this->imageBaseURL}/w500" . $movie['poster_path'];
        $movie['backdrop_full_path'] = "{$this->imageBaseURL}/w780" . $movie['backdrop_path'];

        return response()->json($movie);  // Devolver los detalles de la película
    }

    /**
     * Buscar películas por título
     */
    public function searchMovies(Request $request)
    {
        $query = $request->query('query');  // Obtener el parámetro de búsqueda desde la URL

        // Petición HTTP para realizar la búsqueda de películas
        $response = Http::get("{$this->baseURL}/search/movie", [
            'api_key' => $this->apiKey,
            'language' => 'es-ES',  // Lenguaje de los resultados
            'query' => $query,  // Término de búsqueda
        ]);

        $movies = $response->json();  // Obtener la respuesta en formato JSON

        // Agregar URLs completas de imágenes
        foreach ($movies['results'] as &$movie) {
            $movie['poster_full_path'] = "{$this->imageBaseURL}/w500" . $movie['poster_path'];
            $movie['backdrop_full_path'] = "{$this->imageBaseURL}/w780" . $movie['backdrop_path'];
        }

        return response()->json($movies);  // Devolver los resultados de la búsqueda
    }

    /**
     * Almacenar película desde la API
     */
    public function storeMovieFromAPI($movieExternalId)
    {
        try {
            // Llamada a la API para obtener los detalles de la película
            $response = Http::get("{$this->baseURL}/movie/{$movieExternalId}", [
                'api_key' => $this->apiKey,
                'language' => 'es-ES',
            ]);
    
            $movie = $response->json();
    
            // Verificar si la película ya existe por external_id
            $existingMovie = Movie::where('external_id', $movie['id'])->first();
    
            if (!$existingMovie) {
                // Verificar si la película tiene una imagen (poster_path)
                $imagePath = null;
                if (isset($movie['poster_path'])) {
                    // Crear la URL completa de la imagen
                    $imagePath = "https://image.tmdb.org/t/p/w500" . $movie['poster_path'];
                }
    
                // Crear la película si no existe
                $newMovie = Movie::create([
                    'title' => $movie['title'],
                    'description' => $movie['overview'],
                    'release_date' => $movie['release_date'],
                    'duration' => $movie['runtime'],
                    'external_id' => $movie['id'],  // Guardar el external_id
                    'image_path' => $imagePath,  // Guardar la URL de la imagen
                    'is_new_movie' => true,  // Marcar la película como nueva
                ]);
    
                return response()->json($newMovie, 201);
            } else {
                return response()->json(['message' => 'Película ya existe'], 400);
            }
        } catch (\Exception $e) {
            // Registrar el error
            \Log::error("Error al guardar la película: " . $e->getMessage());
            return response()->json(['message' => 'Error al guardar la película'], 500);
        }
    }
    


    
}