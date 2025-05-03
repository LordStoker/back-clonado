<?php

namespace App\Http\Controllers\Api;

use App\Models\RouteImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class RouteImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $images = RouteImage::all();
        return response()->json(['success' => true, 'data' => $images], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'url' => 'required|image|max:2048',
        ]);

        $imagePath = $request->file('image')->store('route_images', 'public');

        $routeImage = RouteImage::create([
            'route_id' => $validatedData['route_id'],
            'url' => $imagePath,
        ]);

        return response()->json(['success' => true, 'data' => $routeImage], 201);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $image = RouteImage::find($id);

        if (!$image) {
            return response()->json(['success' => false, 'message' => 'Imagen de ruta no encontrada.'], 404);
        }

        return response()->json(['success' => true, 'data' => $image], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $image = RouteImage::find($id);

        if (!$image) {
            return response()->json(['success' => false, 'message' => 'Imagen no encontrada.'], 404);
        }

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return response()->json(['success' => true, 'message' => 'Imagen eliminada.'], 204);
    }
}
