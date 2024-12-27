<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

    public function show($id){

        $user = Client::find($id);
        
        if(!$user) {
            return response()->json(['message'=>'Usuario não encontrado'], 404);
        }
        return response()->json($user);
    }

    public function all(){
        $clients = Client::all();
        return $clients;
    }

    public function update(ClientUpdateRequest $request, $id)
    {
        $client = Client::findOrFail($id);

        $client->update($request->all());

        return response()->json([
            'message' => 'Usuario atualizado com sucesso.',
            'client' => $client,
        ]);
    }

    public function delete($id){
        $client = Client::find($id);
        Client::destroy($id);
        return response()->json([
            'message'=> 'Usuario deletado com sucesso',
            'client'=> $client,
        ], 200);
    }

}
