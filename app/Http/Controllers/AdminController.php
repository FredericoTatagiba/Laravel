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
    public function insert(Request $r){
        $validated = $r->validate([
            'name'=> 'required|string|max:255',
            'email'=> 'required|email|unique:users',
            'password'=> 'required|min:8'
        ]);

        $validated['password'] = bcrypt($validated['password']); // Encripta a senha

        $admin = Admin::create($validated);

        $token = JWTAuth::fromUser($admin);

        return response()->json([
            'message' => 'Usuário criado com sucesso',
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

    public function read(Request $r, $id){
        $admin = Admin::find($id);
        if(!$admin){return response()->json(['message'=>'Usuario não encontrado', 404]);}
        return response()->json($admin);

    //     $product = Product::find($r);
    //     if(!$product){return response()->json(['message'=>'Produto não existe', 404]);}

    //     return response()->json($product);
    // }
    }

    public function all(Request $r){
        $admin = new Admin();
        $admin= $admin->all();
        return $admin;
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'password' => 'nullable|min:8',
        ]);

        $admin = Admin::findOrFail($id);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']); // Atualiza a senha criptografada
        }

        $admin->update($validated);

        return response()->json([
            'message' => 'Usuario atualizado com sucesso.',
            'user' => $admin,
        ]);

        // $admin = Admin::find(1);
        // $admin->name='Atualizado';
        // $admin->save();
        //     return $admin;
    }

    public function delete(Request $r, $id){
        Admin::find($id)->delete();
        return response()->json(['message'=> 'Usuario deletado com sucesso'], 200);
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

    public function change_discount(Request $r, $id){
        $validated = $r->validate([
            'discountOver50' => 'nullable|numeric|min:0|max:100',
            'discountOver100' => 'nullable|numeric|min:0|max:100',
            'discountOver150' => 'nullable|numeric|min:0|max:100',
            'discountOver200'=> 'nullable|numeric|min:0|max:100',
        ]);
        $admin = Admin::findOrFail($id);

        $admin->update($validated);

        // Preparar a resposta com os valores de desconto
        $discounts = collect($validated)->only([
            'discountOver50',
            'discountOver100',
            'discountOver150',
            'discountOver200'
        ])->filter();

        return response()->json([
            'message' => 'Descontos alterados com sucesso.',
            'discounts' => $discounts,
        ], 200);
    }
}
