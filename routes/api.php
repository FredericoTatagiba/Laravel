<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::get('/', function (Request $request) {
//     return response()->json();
// });

// Route::middleware('auth:api')->get('user', function (Request $request) {
//     return $request->user();
// });

//Rotas Administrador
Route::post('admin/cadastrar', [AdminController::class,'insert']);
Route::post('admin/login', [AdminController::class,'login']);
Route::get('admin/refresh', [AdminController::class,'refresh']);
Route::get('admin/ver/{id}', [AdminController::class,'read']);
Route::get('admin/verTodos', [AdminController::class,'all']);
Route::put('admin/atualizar/{id}', [AdminController::class,'update']);
Route::put('admin/alterar_descontos/{id}', [AdminController::class,'change_discount']);
Route::delete('admin/deletar/{id}', [AdminController::class,'delete']);

//Rotas Usuário
Route::post('usuario/cadastrar', [UserController::class,'insert']);
Route::post('usuario/login', [UserController::class,'login']);
Route::get('usuario/refresh', [UserController::class,'refresh']);
Route::get('usuario/ver/{id}', [UserController::class,'read']);
Route::get('usuario/verTodos', [UserController::class,'all']);
Route::put('usuario/atualizar/{id}', [UserController::class,'update']);
Route::delete('usuario/deletar/{id}', [UserController::class,'delete']);

// Rotas Produtos (Protegidas com JWT e acessado apenas com admin)
Route::middleware('auth:admin')->group(function () {
    Route::post('produto/cadastrar', [ProductController::class,'insert']);
    Route::get('produto/ver/{id}', [ProductController::class,'read']);
    Route::get('produto/verTodos', [ProductController::class,'all']);
    Route::put('produto/atualizar/{id}', [ProductController::class,'update']);
    Route::delete('produto/deletar/{id}', [ProductController::class,'delete']);
});

// Rotas Pedidos (Protegidas com JWT)
Route::middleware('auth:api')->group(function () {
    Route::post('pedido/fazer', [OrderController::class, 'insert']);
    Route::get('pedido/ver/{id}', [OrderController::class, 'read']);
    Route::get('pedido/verTodos', [OrderController::class, 'all']);
    Route::put('pedido/atualizar/{id}', [OrderController::class, 'update']);
    Route::delete('pedido/cancelar/{id}', [OrderController::class, 'delete']);
});

