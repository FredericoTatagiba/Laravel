<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminFormRequest;
use App\Models\Admin;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AdminController extends Controller
{
    public function __construct()
    {
        // Protege todas as rotas, exceto o login e registro
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function register(AdminFormRequest $request){
        $request['password'] = bcrypt($request['password']); // Encripta a senha

        $admin = Admin::create($request->all());

        $token = JWTAuth::fromUser($admin);

        return response()->json([
            'message' => 'Administrador criado com sucesso',
            'user' => $admin,
            'token' => $token,
        ], 201);
    }

    public function update(AdminFormRequest $request){
        $admin = Admin::findOrFail($request->id);
        if($admin->update($request->all())){
            return response()->json([
                'message' => 'Administrador atualizado com sucesso',
                'user' => $admin,
            ]);
        }else{
            return response()->json([
                'message' => 'Falha ao atualizar administrador',
                'user' => $admin,
            ]);
        }
    }

    public function delete(Admin $admin){ 
        if($admin->delete()){
            return response()->json([
                'message' => 'Administrador deletado com sucesso',
            ]);
        }else{
            return response()->json([
                'message' => 'Falha ao deletar administrador',
            ]);
        }
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
