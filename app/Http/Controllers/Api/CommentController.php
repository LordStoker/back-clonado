<?php

namespace App\Http\Controllers\Api;

use App\Models\Comment;
use Illuminate\Http\Request;
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

        // Buscar comentarios para la ruta especificada
        $comments = Comment::with(['user'])
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
        ]);

        // Preparar los datos del comentario
        $commentData = $request->only(['comment', 'score', 'route_id']);
        
        // Asignar el ID del usuario autenticado
        $commentData['user_id'] = $request->user()->id;
        
        // Crear el comentario con los datos validados y el user_id del usuario autenticado
        $comment = Comment::create($commentData);

        // Cargar la relación del usuario para incluirla en la respuesta
        $comment->load('user');

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
        $comment = Comment::with(['user', 'route'])->find($id);

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
        // Find the comment by ID
        $comment = Comment::find($id);

        // If the comment is not found, return a 404 response
        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Comment not found'
            ], 404);
        }

        // Delete the comment
        $comment->delete();

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully'
        ], 200);
    }
}
