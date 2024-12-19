<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AdminController extends Controller
{
    public function __construct()
    {
        // Protege todas as rotas, exceto o login e registro
        $this->middleware('auth:api', ['except' => ['login', 'insert']]);
    }

    public function create(Request $request){
        $validated = $request->validate([
            'name'=> 'required|string|max:255',
            'email'=> 'required|email|unique:users',
            'password'=> 'required|min:8'
        ]);

        $validated['password'] = bcrypt($validated['password']); // Encripta a senha

        $admin = Admin::create($validated);

        $token = JWTAuth::fromUser($admin);

        return response()->json([
            'message' => 'Administrador criado com sucesso',
            'user' => $admin,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Credenciais inválidas'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Não foi possível criar o token'], 500);
        }

        return $this->respondWithToken($token);
    }

    public function respondWithToken($token)
    {
        // Obtém o TTL configurado no arquivo jwt.php
        $ttl = config('jwt.ttl'); // Pegando o valor de TTL diretamente da configuração

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $ttl * 60, // Multiplicando por 60 para converter em segundos
        ]);
    }

    public function logout()
    {
        try {
            // Pega o token atual do usuário
            $token = JWTAuth::getToken();

            // Invalida o token
            JWTAuth::invalidate($token);

            return response()->json(['message' => 'Logout realizado com sucesso']);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Não foi possível realizar o logout'], 500);
        }
    }

    public function refresh()
    {
        $newToken = JWTAuth::refresh();
        return $this->respondWithToken($newToken);
    }


}
