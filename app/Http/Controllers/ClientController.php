<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientFormRequest;
use App\Http\Requests\ClientUpdateRequest;
use App\Models\Client;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;

//Logica do Usuario. falta o JWT
class ClientController extends Controller
{
    public function store(ClientFormRequest $request){
        try{
            $user = Client::create($request->all());
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

    public function read($id){

        $user = Client::find($id);
        
        if(!$user) {
            return response()->json(['message'=>'Usuario não encontrado', 404]);
        }
        return response()->json($user);
    }

    public function all(){
        $clients = Client::all();
        return $clients;
    }

    public function update(ClientUpdateRequest $request, $id)
    {
        $user = Client::findOrFail($id);

        $user->update($request->all());

        return response()->json([
            'message' => 'Usuario atualizado com sucesso.',
            'user' => $user,
        ]);
    }

    public function delete($id){
        Client::find($id)->delete();
        return response()->json(['message'=> 'Usuario deletado com sucesso'], 200);
    }

}
