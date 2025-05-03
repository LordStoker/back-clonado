<?php

namespace App\Http\Controllers\Api;

use App\Models\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRouteRequest;
use App\Http\Requests\UpdateRouteRequest;

class RouteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $routes = Route::with(['landscape', 'difficulty', 'user', 'terrain'])->get();
        return response()->json([
            'success' => true,
            'data' => $routes
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRouteRequest $request)
    {
        // Obtenemos los datos validados del request
        $validated = $request->validated();
        
        // Asignamos el ID del usuario autenticado
        $validated['user_id'] = $request->user()->id;
        
        // Creamos la ruta con los datos validados y el user_id actual
        $route = Route::create($validated);
        
        return response()->json($route, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Route $route)
    {
        $route = Route::with(['landscape', 'difficulty', 'user', 'terrain'])->find($route->id);
        if (!$route) {
            return response()->json([
                'success' => false,
                'message' => 'Route not found'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $route
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRouteRequest $request, $id)
    {
        $route = Route::find($id);

        if (!$route) {
            return response()->json(['message' => 'Ruta no encontrada'], 404);
        }

        $route->update($request->validated());
        return response()->json($route, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $route = Route::find($id);

        if (!$route) {
            return response()->json(['message' => 'Ruta no encontrada'], 404);
        }

        $route->delete();
        return response()->json(['message' => 'Ruta eliminada'], 204);
    }
}
