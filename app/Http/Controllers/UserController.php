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
            'cpf'=> 'required|string|digits:11',
            'email'=> 'required|email|unique:users'
        ]);


        $user = User::create($validated);

        return response()->json([
            'message' => 'Usuário criado com sucesso',
            'user' => $user,
        ], 201);
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
