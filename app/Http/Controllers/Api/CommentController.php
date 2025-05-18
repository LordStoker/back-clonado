<?php

namespace App\Http\Controllers\Api;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all comments from the database
        $comments = Comment::with(['user', 'route'])->get();

        // Return the comments as a JSON response
        return response()->json([
            'success' => true,
            'data' => $comments
        ], 200);
    }

    /**
     * Get paginated comments for a specific route.
     */
    public function getRouteComments(Request $request, $routeId)
    {
        // Validar el ID de la ruta
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'perPage' => 'sometimes|integer|min:1|max:50',
        ]);

        // Verificar que la ruta existe
        if (!\App\Models\Route::find($routeId)) {
            return response()->json([
                'success' => false,
                'message' => 'Ruta no encontrada',
                'data' => [],
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 10,
                    'total' => 0,
                ]
            ], 404);
        }

        // Configurar la paginación
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);

        // Buscar comentarios para la ruta especificada, incluyendo usuario e imágenes
        $comments = Comment::with(['user', 'commentImages'])
            ->where('route_id', $routeId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        // Devolver los comentarios paginados
        return response()->json([
            'success' => true,
            'data' => $comments->items(),
            'pagination' => [
                'current_page' => $comments->currentPage(),
                'last_page' => $comments->lastPage(),
                'per_page' => $comments->perPage(),
                'total' => $comments->total(),
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'comment' => 'required|string|max:255',
            'score' => 'required|integer|min:1|max:5',
            // Eliminamos user_id de la validación ya que se asignará automáticamente
            'route_id' => 'required|exists:routes,id',
            'images.*' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048', // Max 2MB por imagen
        ]);

        // Preparar los datos del comentario
        $commentData = $request->only(['comment', 'score', 'route_id']);
        
        // Asignar el ID del usuario autenticado
        $commentData['user_id'] = $request->user()->id;
        
        // Crear el comentario con los datos validados y el user_id del usuario autenticado
        $comment = Comment::create($commentData);

        // Procesar las imágenes si existen
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // Guardar la imagen en el disco público en la carpeta 'comment_images'
                $path = $image->store('comment_images', 'public');
                
                // Solo guardar la ruta del archivo, no la URL completa
                // En el frontend se construirá la URL completa
                $comment->commentImages()->create([
                    'url' => $path,
                    'comment_id' => $comment->id
                ]);
            }
        }

        // Cargar la relación del usuario y las imágenes para incluirlas en la respuesta
        $comment->load(['user', 'commentImages']);

        // Return the created comment as a JSON response
        return response()->json([
            'success' => true,
            'data' => $comment
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Find the comment by ID
        $comment = Comment::with(['user', 'route', 'commentImages'])->find($id);

        // If the comment is not found, return a 404 response
        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Comment not found'
            ], 404);
        }

        // Return the comment as a JSON response
        return response()->json([
            'success' => true,
            'data' => $comment
        ], 200);
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
        // Find the comment by ID with its images
        $comment = Comment::with('commentImages')->find($id);

        // If the comment is not found, return a 404 response
        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Comment not found'
            ], 404);
        }

        // Eliminar los archivos físicos de las imágenes si existen
        foreach ($comment->commentImages as $image) {
            try {
                // Convertir la URL absoluta a ruta relativa
                $urlParts = parse_url($image->url);
                $pathOnly = isset($urlParts['path']) ? $urlParts['path'] : '';
                $storagePath = str_replace('/storage/', '', $pathOnly);
                
                // Verificar si el archivo existe y eliminarlo
                if ($storagePath && \Storage::disk('public')->exists($storagePath)) {
                    \Storage::disk('public')->delete($storagePath);
                }
            } catch (\Exception $e) {
                \Log::error('Error al eliminar imagen: ' . $e->getMessage());
            }
        }

        // Delete the comment (y sus imágenes gracias a onDelete cascade en la migración)
        $comment->delete();

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully'
        ], 200);
    }

    /**
     * Get paginated comments for a specific user.
     */
    public function getUserComments(Request $request, $userId)
    {
        // Validar el ID del usuario y parámetros de paginación
        $request->validate([
            'page' => 'sometimes|integer|min:1',
            'perPage' => 'sometimes|integer|min:1|max:50',
        ]);

        // Verificar que el usuario existe
        if (!\App\Models\User::find($userId)) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado',
                'data' => [],
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 10,
                    'total' => 0,
                ]
            ], 404);
        }

        // Configurar la paginación
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);

        // Buscar comentarios del usuario especificado, incluyendo ruta e imágenes
        $comments = Comment::with(['route', 'commentImages'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        // Asegurarse de que los comentarios incluyan la información de la ruta
        $commentsWithRoutes = $comments->items();
        
        // Verificar que cada comentario tenga una ruta válida
        foreach ($commentsWithRoutes as $comment) {
            if (!$comment->route) {
                // Cargar la relación de ruta si no existe
                $comment->load('route');
            }
        }
        
        // Devolver los comentarios paginados con estructura consistente
        return response()->json([
            'success' => true,
            'data' => $commentsWithRoutes,
            'pagination' => [
                'current_page' => $comments->currentPage(),
                'last_page' => $comments->lastPage(),
                'per_page' => $comments->perPage(),
                'total' => $comments->total(),
            ]
        ], 200);
    }
}
