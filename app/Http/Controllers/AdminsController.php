<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AdminsController extends Controller
{
    public function __construct()
    {
        // Protege todas as rotas, exceto o login e registro
        $this->middleware('auth:admin', ['except' => ['login', 'insert']]);
    }

    public function insert(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins',
            'password' => 'required|min:8',
            'discountOver50'=> 'nullable|numeric|min:0|max:100',
            'discountOver100'=> 'nullable|numeric|min:0|max:100',
            'discountOver150'=> 'nullable|numeric|min:0|max:100',
            'discountOver200'=> 'nullable|numeric|min:0|max:100',
        ]);

        $validated['password'] = bcrypt($validated['password']); // Encripta a senha

        $admin = Admin::create($validated);

        $token = JWTAuth::fromUser($admin);

        return response()->json([
            'message' => 'Administrador criado com sucesso',
            'admin' => $admin,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = auth('admin')->attempt($credentials)) {
                return response()->json(['error' => 'Credenciais inválidas'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Não foi possível criar o token'], 500);
        }

        return $this->respondWithToken($token);
    }

    public function read(Request $request, $id)
    {
        $admin = Admin::find($id);
        if (!$admin) {
            return response()->json(['message' => 'Administrador não encontrado'], 404);
        }
        return response()->json($admin);
    }

    public function all(Request $request)
    {
        $admins = Admin::all();
        return response()->json($admins);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:admins,email,' . $id,
            'password' => 'nullable|min:8',
        ]);

        $admin = Admin::findOrFail($id);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']); // Atualiza a senha criptografada
        }

        $admin->update($validated);

        return response()->json([
            'message' => 'Administrador atualizado com sucesso.',
            'admin' => $admin,
        ]);
    }

    public function delete(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);
        $admin->delete();

        return response()->json(['message' => 'Administrador deletado com sucesso'], 200);
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
            auth('admin')->logout(); // Use a guard 'admin'
            return response()->json(['message' => 'Logout realizado com sucesso']);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Não foi possível realizar o logout'], 500);
        }
    }


    public function refresh()
    {
        try {
            // Usando JWTAuth diretamente para refrescar o token
            $newToken = JWTAuth::refresh(JWTAuth::getToken());

            return $this->respondWithToken($newToken);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Não foi possível atualizar o token'], 500);
        }
    }

    //Função para alteração APENAS dos descontos
    public function change_discount(Request $request, $id){
        $validated = $request->validate([
            'discountOver50'=> 'nullable|numeric|min:0|max:100',
            'discountOver100'=> 'nullable|numeric|min:0|max:100',
            'discountOver150'=> 'nullable|numeric|min:0|max:100',
            'discountOver200'=> 'nullable|numeric|min:0|max:100',
        ]);

        Admin::findOrFail($id)->update($validated);

        return response()->json([
            'message' => 'Descontos alterados com sucesso.',
        ],200);

    }


}
