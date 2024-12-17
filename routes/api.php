<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\MovieFunctionController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

// Rutas públicas
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);

// El admin puede ver los usuarios que se han registrado
Route::prefix('user')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/', [RegisterController::class, 'index']);  // Obtener todos los usuarios
    Route::get('/{user}', [RegisterController::class, 'show']);
    Route::delete('/{user}', [RegisterController::class, 'destroy']);
});

// Ruta protegida para cerrar sesión
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Rutas públicas para consultar películas desde la API externa
Route::prefix('movies')->group(function () {
    Route::get('/popular', [MovieController::class, 'getPopularMovies']); // Películas populares
    Route::get('/{id}', [MovieController::class, 'getMovieDetails']);     // Detalles de película por ID
    Route::get('/search', [MovieController::class, 'searchMovies']);      // Buscar películas
});


// Rutas protegidas para salas (Rooms)
Route::prefix('rooms')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('/', [RoomController::class, 'store']);
    Route::get('/', [RoomController::class, 'index']);
    Route::get('/{room}', [RoomController::class, 'show']);
    Route::put('/{room}', [RoomController::class, 'update']);
    Route::delete('/{room}', [RoomController::class, 'destroy']);
    // Ruta para actualizar los asientos de una sala
    Route::put('/{room}/seats', [RoomController::class, 'updateSeats']);
});

// Rutas protegidas para las películas
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('/movies/store-from-api/{movieExternalId}', [MovieController::class, 'storeMovieFromAPI']);  // Guardar película desde la API
});
// Ruta para obtener solo las películas nuevas

// Rutas protegidas para funciones de películas
Route::prefix('movie-functions')->group(function () {
    Route::middleware(['auth:sanctum', 'role:user'])->group(function () {
        Route::get('/', [MovieFunctionController::class, 'index']);  // Ver funciones
        Route::get('/{movie_function}', [MovieFunctionController::class, 'show']);
    });
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::post('/', [MovieFunctionController::class, 'store']);  // Administrar funciones
        Route::put('/{movie_function}', [MovieFunctionController::class, 'update']);
        Route::delete('/{movie_function}', [MovieFunctionController::class, 'destroy']);
    });
});


Route::prefix('tickets')->group(function () {

    // Rutas para clientes (usuarios)
    Route::middleware(['auth:sanctum', 'role:user'])->group(function () {
        Route::post('/', [TicketController::class, 'store']);  // Comprar ticket (cliente)
        Route::get('/user', [TicketController::class, 'showUserTickets']); // Mostrar tickets por nombre de usuario (Función necesaria en el controlador)
    });

    // Rutas para empleados
    Route::middleware(['auth:sanctum', 'role:empleado'])->group(function () {
        Route::get('/codigo/{ticketCode}', [TicketController::class, 'showByCode']);  // Buscar ticket por código
    });

    // Rutas para administradores
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::get('/', [TicketController::class, 'index']);  // Administrar tickets (listado)
        Route::delete('/{ticketId}', [TicketController::class, 'destroy']);  // Eliminar ticket por ID
    });

});




// Rutas protegidas para Accounts
Route::prefix('accounts')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/', [AccountController::class, 'index']);
    Route::post('/', [AccountController::class, 'store']);
    Route::get('/{accounts}', [AccountController::class, 'show']);
    Route::put('/{accounts}', [AccountController::class, 'update']);
    Route::delete('/{accounts}', [AccountController::class, 'destroy']);
});
