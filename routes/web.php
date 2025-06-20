<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderListController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/menu/{qr_code}', [MenuController::class, 'show'])->name('menu');
Route::get('/order-list/{qr_code}', [OrderListController::class, 'show'])->name('order-list');