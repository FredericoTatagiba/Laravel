<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientFormRequest;
use App\Http\Requests\ClientUpdateRequest;
use App\Models\Client;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Exception;

class ClientController extends Controller
{
    public function store(ClientFormRequest $request){
        try {
            $client = Client::create($request->all());
            return response()->json([
                'message' => 'Usuário criado com sucesso',
                'client' => $client,
            ], 201);
        } catch (Exception $e) {
            return response()->json(['message' => 'Erro ao criar usuário', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id){
        try {
            $client = Client::find($id);
            
            if(!$client) {
                return response()->json(['message'=>'Usuario não encontrado'], 404);
            }
            return response()->json($client);
        } catch (Exception $e) {
            return response()->json(['message' => 'Erro ao buscar usuário', 'error' => $e->getMessage()], 500);
        }
    }

    public function index(Request $request){
        try {
            if($request->has('name')){
                $clients = Client::where('name', 'like', '%' . $request->name . '%')
                                ->paginate(5);
                return response()->json($clients);
            }
            if($request->has('cpf')){
                $clients = Client::where('cpf', 'like', '%' . $request->cpf . '%')
                                ->paginate(5);
                return response()->json($clients);
            }
            if($request->has('email')){
                $clients = Client::where('email', 'like', '%' . $request->email . '%')
                                ->paginate(5);
                return response()->json($clients);
            }

            $clients = Client::paginate(5);
            return response()->json($clients);
        } catch (Exception $e) {
            return response()->json(['message' => 'Erro ao buscar clientes', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(ClientUpdateRequest $request, $id)
    {
        try {
            $client = Client::findOrFail($id);

            $client->update($request->all());

            return response()->json([
                'message' => 'Usuario atualizado com sucesso.',
                'client' => $client,
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Erro ao atualizar usuário', 'error' => $e->getMessage()], 500);
        }
    }

    public function delete($id){
        try {
            $client = Client::find($id);
            if (!$client) {
                return response()->json(['message' => 'Usuario não encontrado'], 404);
            }
            Client::destroy($id);
            return response()->json([
                'message'=> 'Usuario deletado com sucesso',
                'client'=> $client,
            ], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Erro ao deletar usuário', 'error' => $e->getMessage()], 500);
        }
    }
}
