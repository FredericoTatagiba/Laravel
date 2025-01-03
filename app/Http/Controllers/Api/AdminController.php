<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminFormRequest;
use App\Http\Requests\AdminUpdateRequest;
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
        try {
            $request['password'] = bcrypt($request['password']); // Encripta a senha

            $admin = Admin::create($request->all());

            $token = JWTAuth::fromUser($admin);

            return response()->json([
                'message' => 'Administrador criado com sucesso',
                'user' => $admin,
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao registrar administrador'], 500);
        }
    }

    public function login(Request $request){
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

    public function update(AdminUpdateRequest $request, $id){
        $admin = Admin::findOrFail($id);
        try {
            if($request->password){
                $request['password'] = bcrypt($request['password']);
            }

            if($admin->update($request->all())){
                return response()->json([
                    'message' => 'Administrador atualizado com sucesso',
                    'admin' => $admin,
                ]);
            } else{
                return response()->json([
                    'message' => 'Falha ao atualizar administrador',
                    'admin' => $admin,
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar administrador'], 500);
        }
    }

    public function delete($id){
        try {
            $admin = Admin::findOrFail($id);
            if(Admin::destroy($id)){
                return response()->json([
                    'message' => 'Administrador deletado com sucesso',
                    'admin'=> $admin,
                ]);
            } else {
                return response()->json([
                    'message' => 'Falha ao deletar administrador',
                ]);
            }
        } catch(\Exception $e){
            return response()->json([
                'message' => 'Erro ao deletar administrador',
            ], 500);
        }
    }

    public function logout()
    {
        try {
            // Invalida o token
            JWTAuth::invalidate();
            return response()->json(['message' => 'Logout realizado com sucesso']);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Não foi possível realizar o logout'], 500);
        }
    }

    public function respondWithToken($token)
    {
        // Obtém o TTL configurado no arquivo jwt.php
        $ttl = config('jwt.ttl'); // Pegando o valor de TTL diretamente da configuração

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $ttl * 600000, // Multiplicando por 60 para converter em segundos
        ]);
    }

    public function show($id){
        try {
            $admin = Admin::find($id);
            if(!$admin) {
                return response()->json(['message'=>'Administrador não encontrado'], 404);
            }
            return response()->json($admin);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao buscar administrador'], 500);
        }
    }

    public function index(Request $request){
        try {
            if($request->has('name')){
                $admins = Admin::where('name', 'like', '%' . $request->name . '%')
                                ->paginate(5);
                return response()->json($admins);
            }
            if($request->has('email')){
                $admins = Admin::where('email', 'like', '%' . $request->email . '%')
                                ->paginate(5);
                return response()->json($admins);
            }
            //return Admin::paginate(5);
            // $admins = Admin::all();
            $admins = Admin::paginate(5);

            return response()->json($admins);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao buscar administradores'], 500);
        }
    }

    public function refresh()
    {
        try {
            $newToken = JWTAuth::refresh();
            return $this->respondWithToken($newToken);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Não foi possível atualizar o token'], 500);
        }
    }
}
