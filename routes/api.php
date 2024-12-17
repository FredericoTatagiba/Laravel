<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\OrdersController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::get('/', function (Request $request) {
//     return response()->json();
// });

// Route::middleware('auth:api')->get('user', function (Request $r) {
//     return $r->user();
// });

//Rotas Usuário
Route::post('cadastrar', [UsersController::class,'insert']);
Route::post('login', [UsersController::class,'login']);
Route::get('refresh', [UsersController::class,'refresh']);
Route::get('ver/{id}', [UsersController::class,'read']);
Route::get('verTodos', [UsersController::class,'all']);
Route::put('atualizar/{id}', [UsersController::class,'update']);
Route::delete('deletar/{id}', [UsersController::class,'delete']);

// Rotas Produtos (Protegidas com JWT)
Route::middleware('auth:api')->group(function () {
    Route::post('produto/cadastrar', [ProductsController::class,'insert']);
    Route::get('produto/ver/{id}', [ProductsController::class,'read']);
    Route::get('produto/verTodos', [ProductsController::class,'all']);
    Route::put('produto/atualizar/{id}', [ProductsController::class,'update']);
    Route::delete('produto/deletar/{id}', [ProductsController::class,'delete']);
});

// Rotas Pedidos (Protegidas com JWT)
Route::middleware('auth:api')->group(function () {
    Route::post('pedido/fazer', [OrdersController::class,'insert']);
    Route::get('pedido/ver/{id}', [OrdersController::class,'read']);
    Route::get('pedido/verTodos', [OrdersController::class,'all']);
    Route::put('pedido/atualizar/{id}', [OrdersController::class,'update']);
    Route::delete('pedido/cancelar/{id}', [OrdersController::class,'delete']);
});
