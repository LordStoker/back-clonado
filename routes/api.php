<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RouteController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\TerrainController;
use App\Http\Controllers\Api\LandscapeController;
use App\Http\Controllers\Api\DifficultyController;
use App\Http\Controllers\Api\RouteImageController;
use App\Http\Controllers\Api\CommentImageController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// --- RUTAS PÚBLICAS ---
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
// Listar y mostrar información general (no requieren autenticación)
Route::apiResource('terrain', TerrainController::class);
Route::apiResource('difficulty', DifficultyController::class);
Route::apiResource('landscape', LandscapeController::class);
Route::apiResource('country', CountryController::class);
Route::apiResource('role', RoleController::class);
Route::apiResource('route-images', RouteImageController::class)->only(['index', 'show']);
Route::apiResource('comment-images', CommentImageController::class)->only(['index', 'show']);

// Rutas públicas para las rutas (listar y ver una) //BORRARLAS TRAS TERMINAR LAS PRUEBAS DE BACKEND Y FRONTEND
Route::apiResource('route', RouteController::class);

// Rutas públicas para usuarios (listar y ver información de usuarios)
Route::apiResource('user', UserController::class);

// --- RUTAS PROTEGIDAS (requieren login con Sanctum) ---
Route::middleware('auth:sanctum')->group(function () {
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
    //TO DO: Quitar las rutas de user y route de las públicas y poner aquí las de destroy, update y store.
    // Rutas protegidas para creación, modificación y borrado de rutas
    // Route::post('/routes', [RouteController::class, 'store']);
    // Route::put('/routes/{route}', [RouteController::class, 'update']);
    // Route::delete('/routes/{route}', [RouteController::class, 'destroy']);
    
    // Rutas protegidas para actualizar o eliminar usuarios
    // Route::put('/users/{user}', [UserController::class, 'update']);
    // Route::delete('/users/{user}', [UserController::class, 'destroy']);

    // Rutas protegidas para creación, modificación y borrado de imágenes de rutas
    // Route::post('/route-images', [RouteImageController::class, 'store']);
    // Route::delete('/route-images/{route_image}', [RouteImageController::class, 'destroy']);
    // Rutas protegidas para creación, modificación y borrado de imágenes de comentarios
    
    // Route::post('/comment-images', [CommentImageController::class, 'store']);
    // Route::delete('/comment-images/{comment_image}', [CommentImageController::class, 'destroy']);
});
