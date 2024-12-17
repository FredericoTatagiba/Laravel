<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\OrdersController;
use App\Models\Admin;
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

//Rotas Administrador
Route::post('cadastrar', [AdminController::class,'insert']);
Route::post('login', [AdminController::class,'login']);
Route::get('refresh', [AdminController::class,'refresh']);
Route::get('ver/{id}', [AdminController::class,'read']);
Route::get('verTodos', [AdminController::class,'all']);
Route::put('atualizar/{id}', [AdminController::class,'update']);
Route::put('alterar_descontos', [AdminController::class,'change_discount']);
Route::delete('deletar/{id}', [AdminController::class,'delete']);

//Rotas Usuário
Route::post('usuario/cadastrar', [UsersController::class,'insert']);
Route::post('usuario/login', [UsersController::class,'login']);
Route::get('usuario/refresh', [UsersController::class,'refresh']);
Route::get('usuario/ver/{id}', [UsersController::class,'read']);
Route::get('usuario/verTodos', [UsersController::class,'all']);
Route::put('usuario/atualizar/{id}', [UsersController::class,'update']);
Route::delete('usuario/deletar/{id}', [UsersController::class,'delete']);

// Rotas Produtos (Protegidas com JWT)
Route::middleware(['auth:api', 'admin'])->group(function () {
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
