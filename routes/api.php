<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoodsController;
use App\Http\Controllers\IncomingGoodsController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\GoodsBatchController;
use App\Http\Controllers\GraphicsDataController;
use App\Http\Controllers\OutgoingGoodsController;
use App\Http\Controllers\UserController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('login');
Route::get('dashboard', [GraphicsDataController::class, 'dashboardData']);   
Route::get('goods/{id}/unit', [GoodsController::class, 'getGoodsUnit']); 

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('goods/select', [GoodsController::class, 'selectSuggestions']);
    Route::get('goods/{goods}/batches', [GoodsController::class, 'getAvailableGoodsBatches']);
    // Route::get('/goods/get-pdf', [GoodsController::class, 'download'])->name('goods.download');
    Route::resource('goods', GoodsController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
    Route::resource('incoming-goods', IncomingGoodsController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
    Route::resource('outgoing-goods', OutgoingGoodsController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
    Route::resource('supplier', SupplierController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
    Route::resource('category', CategoryController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
    Route::resource('unit', UnitController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
    Route::resource('users', UserController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
    Route::resource('goods-batches', GoodsBatchController::class)->only(['index']);
});
