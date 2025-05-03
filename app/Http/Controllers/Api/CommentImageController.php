<?php

namespace App\Http\Controllers\Api;

use App\Models\CommentImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class CommentImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $images = CommentImage::all();
        return response()->json(['success' => true, 'data' => $images], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'comment_id' => 'required|exists:comments,id',
            'url' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $path = $request->file('image_path')->store('comment_images', 'public');

        $image = CommentImage::create([
            'comment_id' => $request->comment_id,
            'url' => $path,
        ]);

        return response()->json(['success' => true, 'data' => $image], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $image = CommentImage::find($id);

        if (!$image) {
            return response()->json(['success' => false, 'message' => 'Imagen de comentario no encontrada.'], 404);
        }

        return response()->json(['success' => true, 'data' => $image], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $image = CommentImage::find($id);

        if (!$image) {
            return response()->json(['success' => false, 'message' => 'Imagen no encontrada.'], 404);
        }

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return response()->json(['success' => true, 'message' => 'Imagen eliminada.'], 204);
    }
}
