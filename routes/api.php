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
Route::post('register', [AdminController::class,'insert']);
Route::post('login', [AdminController::class,'login']);
Route::get('{id}', [AdminController::class,'read']);
Route::get('verTodos', [AdminController::class,'all']);
Route::put('{id}', [AdminController::class,'update']);
Route::delete('{id}', [AdminController::class,'delete']);

//Rotas Usuário
Route::post('usuario/cadastrar', [UserController::class,'insert']);
Route::get('usuario/{id}', [UserController::class,'read']);
Route::get('usuario/verTodos', [UserController::class,'all']);
Route::put('usuario/{id}', [UserController::class,'update']);
Route::delete('usuario/{id}', [UserController::class,'delete']);

// Rotas Produtos (Protegidas com JWT e acessado apenas com admin)
Route::middleware('auth:api')->group(function () {
    Route::post('produto/cadastrar', [ProductController::class,'insert']);
    Route::get('produto/{id}', [ProductController::class,'read']);
    Route::get('produto/verTodos', [ProductController::class,'all']);
    Route::put('produto/{id}', [ProductController::class,'update']);
    Route::delete('produto/{id}', [ProductController::class,'delete']);
});

// Rotas Pedidos (Protegidas com JWT)
Route::middleware('auth:api')->group(function () {
    Route::post('pedido/fazer', [OrderController::class, 'insert']);
    Route::get('pedido/ver/{id}', [OrderController::class, 'read']);
    Route::get('pedido/verTodos', [OrderController::class, 'all']);
    Route::put('pedido/atualizar/{id}', [OrderController::class, 'update']);
    Route::delete('pedido/cancelar/{id}', [OrderController::class, 'delete']);
});

