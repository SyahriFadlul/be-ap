<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GoodsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IncomingGoodsController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UnitController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::get('/goods/get-pdf', [GoodsController::class, 'download'])->name('goods.download');
Route::resource('goods', GoodsController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
Route::resource('incoming-goods', IncomingGoodsController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
Route::resource('supplier', SupplierController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
Route::resource('category', CategoryController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
Route::resource('unit', UnitController::class)->only(['index', 'show', 'store', 'update', 'destroy']);