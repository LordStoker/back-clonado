<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }

    /**
     * Handle an incoming API password reset link request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiStore(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        try {
            $status = Password::sendResetLink(
                $request->only('email')
            );
            
            // Para API siempre devolvemos una respuesta exitosa, 
            // independientemente de si el email existe o no
            // Esto es una buena práctica de seguridad para evitar 
            // revelar qué correos están registrados en el sistema
            return response()->json([
                'status' => 'success',
                'message' => 'Si el correo existe en nuestra base de datos, recibirás un enlace para restablecer tu contraseña.'
            ]);
            
        } catch (\Exception $e) {
            // Log el error para depuración pero no lo mostramos al usuario
            \Log::error('Error en envío de reseteo de contraseña: ' . $e->getMessage());
            
            // Seguimos devolviendo respuesta exitosa
            return response()->json([
                'status' => 'success',
                'message' => 'Si el correo existe en nuestra base de datos, recibirás un enlace para restablecer tu contraseña.'
            ]);
        }
    }
}
