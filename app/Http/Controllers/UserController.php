<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

//Logica do Usuario. falta o JWT
class UserController extends Controller
{
    public function __construct()
    {
        // Protege todas as rotas, exceto o login e registro
        $this->middleware('auth:api', ['except' => ['login', 'insert']]);
    }
    public function insert(Request $request){
        $validated = $request->validate([
            'name'=> 'required|string|max:255',
            'cpf'=> 'required|integer|digits:11',
            'email'=> 'required|email|unique:users',
            'password'=> 'required|min:8'
        ]);

        $validated['password'] = bcrypt($validated['password']); // Encripta a senha

        $user = User::create($validated);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Usuário criado com sucesso',
            'user' => $user,
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

    public function read(Request $request, $id){
        $user = User::find($id);
        if(!$user){return response()->json(['message'=>'Usuario não encontrado', 404]);}
        return response()->json($user);

    //     $product = Product::find($request);
    //     if(!$product){return response()->json(['message'=>'Produto não existe', 404]);}

    //     return response()->json($product);
    // }
    }

    public function all(Request $request){
        $user = new User();
        $user= $user->all();
        return $user;
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'cpf'=> 'nullable|integer|digits:11',
            'email' => 'nullable|email|unique:users,email,' . $id,
            'password' => 'nullable|min:8',
        ]);

        $user = User::findOrFail($id);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']); // Atualiza a senha criptografada
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Usuario atualizado com sucesso.',
            'user' => $user,
        ]);

        // $user = User::find(1);
        // $user->name='Atualizado';
        // $user->save();
        //     return $user;
    }

    public function delete(Request $request, $id){
        User::find($id)->delete();
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

}
