<?php

namespace App\Http\Controllers\Api;

use App\Models\Route;
use App\Models\FavoriteRoute;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FavoriteRouteController extends Controller
{
    /**
     * Guardar una ruta como favorita
     */
    public function toggleFavorite(Request $request, $routeId)
    {
        $userId = $request->user()->id;
        
        // Verificar si la ruta existe
        $route = Route::find($routeId);
        if (!$route) {
            return response()->json([
                'success' => false,
                'message' => 'Ruta no encontrada'
            ], 404);
        }
        
        // Verificar si ya está marcada como favorita
        $favorite = FavoriteRoute::where('user_id', $userId)
                                ->where('route_id', $routeId)
                                ->first();
        
        if ($favorite) {
            // Si ya existe, la eliminamos (toggle off)
            $favorite->delete();
            return response()->json([
                'success' => true,
                'is_favorite' => false,
                'message' => 'Ruta eliminada de favoritos'
            ]);
        } else {
            // Si no existe, la añadimos (toggle on)
            FavoriteRoute::create([
                'user_id' => $userId,
                'route_id' => $routeId
            ]);
            return response()->json([
                'success' => true,
                'is_favorite' => true,
                'message' => 'Ruta añadida a favoritos'
            ]);
        }
    }
    
    /**
     * Verificar si una ruta es favorita para el usuario actual
     */
    public function checkFavorite(Request $request, $routeId)
    {
        $userId = $request->user()->id;
        
        $isFavorite = FavoriteRoute::where('user_id', $userId)
                                  ->where('route_id', $routeId)
                                  ->exists();
                                  
        return response()->json([
            'success' => true,
            'is_favorite' => $isFavorite
        ]);
    }    /**
     * Método de depuración para verificar la ruta y la autenticación
     */
    public function debugRoute(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'La ruta funciona correctamente',
            'user' => $request->user(),
            'timestamp' => now()
        ]);
    }
    
    /**
     * Obtener todas las rutas favoritas del usuario actual
     */
    public function getUserFavorites(Request $request)
    {
        try {
            $userId = $request->user()->id;
            
            // Primero obtenemos los IDs de las rutas favoritas
            $favoriteRouteIds = FavoriteRoute::where('user_id', $userId)
                ->pluck('route_id');
                  // Luego obtenemos las rutas completas con sus relaciones
            $favoriteRoutes = Route::whereIn('id', $favoriteRouteIds)
                ->with(['landscape', 'difficulty', 'user', 'terrain', 'country', 'routeImages'])
                ->get();
                
            // Nos aseguramos de que el atributo route_images esté disponible
            $favoriteRoutes->each(function ($route) {
                $route->route_images = $route->routeImages;
            });
            
            return response()->json([
                'success' => true,
                'data' => $favoriteRoutes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener rutas favoritas: ' . $e->getMessage()
            ], 500);
        }
    }
}
