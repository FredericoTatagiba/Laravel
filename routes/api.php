<?php

use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\DiscountController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/');
});

//Rotas Administrador
Route::prefix('admin')
    ->controller(AdminController::class)
    ->group(function () {
        Route::post('/register', 'register');
        Route::post('/login','login')->name('login');
        Route::get('/{id}/show','read');
        Route::get('/all','all');
        Route::put('/{id}','update');
        Route::delete( '/{id}','delete');
});

Route::middleware('auth:api')
    ->group(function () {

    //Rotas Clientes
    Route::prefix('client')
    ->controller(ClientController::class)
    ->group(function () {
        Route::post('/register', 'store');
        Route::get('/{id}/show','show');
        Route::get('/all','all');
        Route::put('/{id}','update');
        Route::delete('/{id}','delete');
    });


    // Rotas Produtos
    Route::prefix('product')
    ->controller(ProductController::class)
    ->group(function () {
        Route::post('/register', 'register');
        Route::get('/{id}/show','read');
        Route::get('/all','all');
        Route::put('/{id}','update');
        Route::delete('/{id}','delete');
    });


    // Rotas Pedidos
    Route::prefix('order')
    ->controller(OrderController::class)
    ->group(function () {
        Route::post('/store', 'store');
        Route::get('/{id}/show','read');
        Route::get('/all','all');
        Route::put('/{id}/update','update');
        Route::put('/{id}/cancel','cancel');
        Route::put('/{id}/paid','paid');
    });

    //Rotas Descontos
    Route::prefix('discount')
    ->controller(DiscountController::class)
    ->group(function () {
        Route::post('/register', 'store');
        Route::get('/{id}','show');
        Route::put('/{id}','update');
        Route::delete('/{id}','delete');
    });
});