<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Registro de nuevo usuario
     */
    public function register(Request $request)
    {
        // 1. Validación estricta con los campos de tu BD futboltotal2
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed', // Requiere password_confirmation
            'telefono_usuario' => 'nullable|string|max:20',
            'doc_identidad_usuario' => 'required|string|max:20|unique:users',
            'nombres' => 'required|string|max:35',
            'apellidos' => 'required|string|max:35',
        ]);

        // 2. Creación del usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telefono_usuario' => $request->telefono_usuario,
            'doc_identidad_usuario' => $request->doc_identidad_usuario,
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
        ]);

        // 3. Generación del Token de Sanctum
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    /**
     * Inicio de sesión
     */
    public function login(Request $request)
    {
        // Validación de entrada
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Intento de autenticación
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Las credenciales proporcionadas son incorrectas.'
            ], 401);
        }

        // Obtener usuario autenticado
        $user = User::where('email', $request->email)->firstOrFail();

        // Generar nuevo token
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login exitoso',
            'user' => $user,
            'token' => $token
        ]);
    }

    /**
     * Cerrar sesión (Revocar token)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada y token eliminado'
        ]);
    }
}