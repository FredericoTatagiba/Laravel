<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\DiscountController;
use App\Models\Admin;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/');
});

//Rotas Administrador
//Em tese a rota fica assim /admin/register
Route::prefix('admin')
    ->controller(AdminController::class)
    ->group(function () {
        Route::post('/register', 'register');
        Route::post('/login','login')->name('login');
        Route::get('/{id}','read');
        Route::get('/all','all');
        Route::put('/{id}','update');
        Route::delete('/{id}','destroy');
});

//Rotas Usuário (Protegidas com JWT)
Route::prefix('client')
    ->controller(ClientController::class)
    ->middleware('auth:api')
    ->group(function () {
        Route::post('/register', 'store');
        Route::get('/{id}','read');
        Route::get('/','all');
        Route::put('/{id}','update');
        Route::delete('/{id}','delete');
});


// Rotas Produtos (Protegidas com JWT)
Route::prefix('product')
    ->controller(ProductController::class)
    ->middleware('auth:api')
    ->group(function () {
        Route::post('/register', 'store');
        Route::get('/{id}','read');
        Route::get('/all','all');
        Route::put('/{id}','update');
        Route::delete('/{id}','delete');
});


// Rotas Pedidos (Protegidas com JWT)
Route::prefix('order')
    ->controller(OrderController::class)
    ->middleware('auth:api')
    ->group(function () {
        Route::post('/register', 'store');
        Route::get('/{id}','read');
        Route::get('/all','all');
        Route::put('/{id}','update');
        Route::put('/{id}/cancel','cancel');
        Route::put('/{id}/paid','paid');
});

Route::prefix('discount')
    ->controller(OrderController::class)
    ->middleware('auth:api')
    ->group(function () {
        Route::post('/register', 'store');
        Route::get('/{id}','read');
        Route::put('/{id}','update');
        Route::delete('/{id}','delete');
    });
