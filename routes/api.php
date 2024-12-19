<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AdminsController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\OrdersController;
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
Route::post('admin/cadastrar', [AdminsController::class,'insert']);
Route::post('admin/login', [AdminsController::class,'login']);
Route::get('admin/refresh', [AdminsController::class,'refresh']);
Route::get('admin/ver/{id}', [AdminsController::class,'read']);
Route::get('admin/verTodos', [AdminsController::class,'all']);
Route::put('admin/atualizar/{id}', [AdminsController::class,'update']);
Route::put('admin/alterar_descontos/{id}', [AdminsController::class,'change_discount']);
Route::delete('admin/deletar/{id}', [AdminsController::class,'delete']);

//Rotas Usuário
Route::post('usuario/cadastrar', [UsersController::class,'insert']);
Route::post('usuario/login', [UsersController::class,'login']);
Route::get('usuario/refresh', [UsersController::class,'refresh']);
Route::get('usuario/ver/{id}', [UsersController::class,'read']);
Route::get('usuario/verTodos', [UsersController::class,'all']);
Route::put('usuario/atualizar/{id}', [UsersController::class,'update']);
Route::delete('usuario/deletar/{id}', [UsersController::class,'delete']);

// Rotas Produtos (Protegidas com JWT e acessado apenas com admin)
Route::middleware('auth:admin')->group(function () {
    Route::post('produto/cadastrar', [ProductsController::class,'insert']);
    Route::get('produto/ver/{id}', [ProductsController::class,'read']);
    Route::get('produto/verTodos', [ProductsController::class,'all']);
    Route::put('produto/atualizar/{id}', [ProductsController::class,'update']);
    Route::delete('produto/deletar/{id}', [ProductsController::class,'delete']);
});

// Rotas Pedidos (Protegidas com JWT)
Route::middleware('auth:api')->group(function () {
    Route::post('pedido/fazer', [OrdersController::class, 'insert']);
    Route::get('pedido/ver/{id}', [OrdersController::class, 'read']);
    Route::get('pedido/verTodos', [OrdersController::class, 'all']);
    Route::put('pedido/atualizar/{id}', [OrdersController::class, 'update']);
    Route::delete('pedido/cancelar/{id}', [OrdersController::class, 'delete']);
});

