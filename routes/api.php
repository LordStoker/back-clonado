<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RouteController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\TerrainController;
use App\Http\Controllers\Api\LandscapeController;
use App\Http\Controllers\Api\DifficultyController;
use App\Http\Controllers\Api\RouteImageController;
use App\Http\Controllers\Api\CommentImageController;
use App\Http\Controllers\Api\FavoriteRouteController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// --- RUTAS PÚBLICAS ---
Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login', [AuthenticatedSessionController::class, 'store']);

// Endpoints para recuperación de contraseña
Route::post('/forgot-password', [PasswordResetLinkController::class, 'apiStore']);
Route::post('/reset-password', [NewPasswordController::class, 'apiStore']);

// Listar y mostrar información general (no requieren autenticación)
Route::apiResource('terrain', TerrainController::class);
Route::apiResource('difficulty', DifficultyController::class);
Route::apiResource('landscape', LandscapeController::class);
Route::apiResource('country', CountryController::class);
Route::apiResource('role', RoleController::class);
Route::apiResource('route-images', RouteImageController::class)->only(['index', 'show']);
Route::apiResource('comment-images', CommentImageController::class)->only(['index', 'show']);
Route::apiResource('comment', CommentController::class)->only(['index', 'show']);

// Ruta pública para obtener comentarios de una ruta específica
Route::get('/routes/{route}/comments', [CommentController::class, 'getRouteComments']);

// Ruta pública para obtener comentarios de un usuario específico
Route::get('/users/{user}/comments', [CommentController::class, 'getUserComments']);

// Rutas públicas para las rutas (listar y ver una) //BORRARLAS TRAS TERMINAR LAS PRUEBAS DE BACKEND Y FRONTEND
Route::apiResource('route', RouteController::class)->only(['index', 'show']);

// Rutas públicas para usuarios (listar y ver información de usuarios)
Route::apiResource('user', UserController::class)->only(['index', 'show']);

// Ruta para ver perfil público de usuario
Route::get('/users/{user}/public-profile', [UserController::class, 'publicProfile']);

// --- RUTAS PROTEGIDAS (requieren login con Sanctum) ---
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
    
    // Rutas protegidas para creación, modificación y borrado de rutas
    Route::apiResource('route', RouteController::class)->except(['index', 'show']);
    Route::apiResource('comment', CommentController::class)->only(['store', 'destroy']);
    //Rutas protegidas para actualizar o eliminar usuarios
    Route::put('/user/{user}', [UserController::class, 'update']);
    Route::put('/user/{user}/change-password', [UserController::class, 'changePassword']);
    Route::delete('/user/{user}', [UserController::class, 'destroy']);
    
    // Rutas para favoritos
    // IMPORTANTE: La ruta de favoritos debe estar antes que otras rutas con parámetros que puedan interceptarla
    Route::get('/user/favorites', [FavoriteRouteController::class, 'getUserFavorites']);
    // Creamos una ruta alternativa más específica por si hay conflictos con la anterior
    Route::get('/user-favorite-routes', [FavoriteRouteController::class, 'getUserFavorites']);
    // Nueva ruta optimizada que devuelve solo los IDs de las rutas favoritas
    Route::get('/favorite-route-ids', [FavoriteRouteController::class, 'getFavoriteIds']);
    // Ruta de depuración
    Route::get('/debug-favorites', [FavoriteRouteController::class, 'debugRoute']);
    Route::post('/routes/{route}/favorite', [FavoriteRouteController::class, 'toggleFavorite']);
    Route::get('/routes/{route}/favorite', [FavoriteRouteController::class, 'checkFavorite']);

    // Rutas protegidas para creación, modificación y borrado de imágenes de rutas
    Route::post('/route-images', [RouteImageController::class, 'store']);
    Route::delete('/route-images/{route_image}', [RouteImageController::class, 'destroy']);
    // Rutas protegidas para creación, modificación y borrado de imágenes de comentarios
    
    Route::post('/comment-images', [CommentImageController::class, 'store']);
    Route::delete('/comment-images/{comment_image}', [CommentImageController::class, 'destroy']);
});
