<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return response()->json([
            'success' => true,
            'data' => $users
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user = User::with(['role', 'comments'])->find($user->id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }
    
    /**
     * Display the public profile of a user.
     * 
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function publicProfile(User $user)
    {
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }
        
        // Solo devolvemos información pública (nombre, apellidos)
        $publicData = [
            'id' => $user->id,
            'name' => $user->name,
            'last_name' => $user->last_name,
            'created_at' => $user->created_at
        ];
        
        return response()->json([
            'success' => true,
            'data' => $publicData
        ], 200);
    }
    // {
    //     $user = User::find($id);
    //     if (!$user) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'User not found'
    //         ], 404);
    //     }
    //     return response()->json([
    //         'success' => true,
    //         'data' => $user
    //     ], 200);
    // }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Validar que el usuario autenticado solo pueda modificar su propio perfil
        if ($request->user()->id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'No estás autorizado para modificar este perfil'
            ], 403);
        }
        
        // Validar datos recibidos
        $validator = \Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255|unique:users,email,' . $user->id,
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Actualizar datos del usuario
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        
        if ($request->has('last_name')) {
            $user->last_name = $request->last_name;
        }
        
        if ($request->has('email') && $request->email !== $user->email) {
            $user->email = $request->email;
        }
        
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Perfil actualizado correctamente',
            'data' => $user
        ], 200);
    }

    /**
     * Change the user password.
     */
    public function changePassword(Request $request, User $user)
    {
        // Validar que el usuario autenticado solo pueda modificar su propia contraseña
        if ($request->user()->id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'No estás autorizado para cambiar esta contraseña'
            ], 403);
        }
        
        // Validar datos recibidos
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Verificar que la contraseña actual sea correcta
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'La contraseña actual es incorrecta'
            ], 400);
        }
        
        // Actualizar contraseña
        $user->password = Hash::make($request->password);
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada correctamente'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
