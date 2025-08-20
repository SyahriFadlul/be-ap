<?php

use App\Events\TestBroadcastEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoodsController;
use App\Http\Controllers\IncomingGoodsController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\GoodsBatchController;
use App\Http\Controllers\GraphicsDataController;
use App\Http\Controllers\OutgoingGoodsController;
use App\Http\Controllers\UserController;
use App\Models\SystemNotification;
use Illuminate\Support\Facades\Broadcast;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:sanctum')
    ->name('logout');
Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('login');
Route::get('dashboard', [GraphicsDataController::class, 'dashboardData']);   
Route::get('goods/{id}/unit', [GoodsController::class, 'getGoodsUnit']); 
Route::get('outgoing-goods/available-goods/{id}',[OutgoingGoodsController::class,'searchAvailableGoods']);
// Route::get('testeringago',[GoodsController::class,'updatebatchestobase']);
// Route::get('pricingango',[GoodsController::class,'updatepricegoodsbatches']);
Route::get('/expiring-goods', [GoodsController::class, 'expiringGoods']);
Route::get('/incoming-goods/download', [IncomingGoodsController::class, 'downloadIncomingGoods']);
Route::post('/incoming-goods/export-excel', [IncomingGoodsController::class, 'exportIncomingGoodsExcel']);
Route::get('/outgoing-goods/report', [OutgoingGoodsController::class, 'download']);


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('goods/search', [GoodsController::class, 'search']);
    Route::get('outgoing-goods/search', [OutgoingGoodsController::class, 'search']);
    Route::get('incoming-goods/search', [IncomingGoodsController::class, 'search']);
    Route::get('suppliers/search', [SupplierController::class, 'search']);
    Route::get('categories/search', [CategoryController::class, 'search']);
    Route::get('goods/select', [GoodsController::class, 'selectSuggestions']);
    Route::post('goods/{goods}/batches', [GoodsController::class, 'getAvailableGoodsBatches']);
    Route::post('user/{id}/role', [UserController::class,'getUserRole']);
    Route::get('role', [UserController::class,'getRoles']);
    Route::post('incoming-goods/filter', [IncomingGoodsController::class, 'getFilteredData']);
    Route::post('outgoing-goods/export-pdf', [OutgoingGoodsController::class, 'exportOutgoingGoodsToPDF']);
    Route::post('incoming-goods/export-pdf', [IncomingGoodsController::class, 'exportIncomingGoodsToPDF']);
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

Broadcast::routes();

Route::get('/test-broadcast', function () {
    broadcast(new \App\Events\InventoryNotification(
        'Tes Notifikasi',
        'Notifikasi dari Laravel Echo Server berhasil!'
    ));
    return 'Broadcast sent!';
});


Route::get('/broadcast-test', function () {
    broadcast(new TestBroadcastEvent('halo dunia!'));
    return 'Event terkirim';
});


Route::get('/notifications', function () {
    $notifications = SystemNotification::where('read', false)
        ->latest()
        ->take(10)
        ->get();

    // Tandai sebagai sudah dibaca (opsional)
    SystemNotification::whereIn('id', $notifications->pluck('id'))->update(['read' => true]);

    return $notifications;
});