<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;


//Logica do Usuario. falta o JWT
class UsersController extends Controller
{
    public function insert(Request $r){
        $validated = $r->validate([
            'name'=> 'required|string|max:255',
            'cpf'=> 'required|integer|digits:11',
            'email'=> 'required|email|unique:users',
            'password'=> 'required|min:8'
        ]);

        $validated['password'] = bcrypt($validated['password']); // Encripta a senha

        User::create($validated);

        return response()->json(['message'=>'Usuário criado com sucesso'],200);
    }

    public function read(Request $r, $id){
        $user = User::find($id);
        if(!$user){return response()->json(['message'=>'Usuario não encontrado', 404]);}
        return response()->json($user);

    //     $product = Product::find($r);
    //     if(!$product){return response()->json(['message'=>'Produto não existe', 404]);}

    //     return response()->json($product);
    // }
    }

    public function all(Request $r){
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

public function delete(Request $r, $id){
    User::find($id)->delete();
    return response()->json(['message'=> 'Usuario deletado com sucesso'], 200);
}



}
