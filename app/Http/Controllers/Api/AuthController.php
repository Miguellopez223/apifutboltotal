<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //

    

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function register(Request $request)
    {
        // 1. Validación
        $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'telefono_usuario' => 'required|string|max:20',
            'doc_identidad_usuario' => 'required|string|max:20',
            'nombres' => 'required|string|max:50',
            'apellidos' => 'required|string|max:50',
        ]);

        // 2. Crear usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telefono_usuario' => $request->telefono_usuario,
            'doc_identidad_usuario' => $request->doc_identidad_usuario,
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos
        ]);

        // 3. Crear token
        $token = $user->createToken('api-token')->plainTextToken;

        // 4. Respuesta
        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }
}