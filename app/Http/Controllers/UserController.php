<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientFormRequest;
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
    public function store(ClientFormRequest $request){
        try{
            $user = User::create($request->all());
            return response()->json([
                'message' => 'Usuário criado com sucesso',
                'user' => $user,
            ], 201);

        } catch(JWTException $e) {
            return response()->json([
                'message' => 'Falha ao criar usuário',
            ], 500);
        }
    }

    public function read(Request $request, $id){

        $user = User::find($id);
        
        if(!$user) {
            return response()->json(['message'=>'Usuario não encontrado', 404]);
        }

        return response()->json($user);
    }

    public function all(Request $request){
        // $user = new User();
        // $user= $user->all();

        return User::get();
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'cpf'=> 'nullable|string|digits:11',
            'email' => 'nullable|email|unique:users,email,' . $id,
        ]);

        $user = User::findOrFail($id);

        $user->update($validated);

        return response()->json([
            'message' => 'Usuario atualizado com sucesso.',
            'user' => $user,
        ]);
    }

    public function delete(Request $request, $id){
        User::find($id)->delete();
        return response()->json(['message'=> 'Usuario deletado com sucesso'], 200);
    }

}
