<?php

use App\Events\InventoryNotification;
use App\Http\Controllers\GoodsController;
use App\Models\SystemNotification;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule::call(function () {
//     $goodsCon = new GoodsController();
//     $response = $goodsCon->expiringGoods(); // response = json()

//     $expiring = collect($response->getData()); // konversi jadi koleksi

//     if ($expiring->count() > 0) {
//         SystemNotification::create([
//             'title' => 'Barang Akan Kedaluwarsa',
//             'message' => 'Ada ' . $expiring->count() . ' barang kadaluwarsa dalam 7 hari.',
//             'type' => 'warning',
//         ]);
//     }
// })->dailyAt('11:05');

Schedule::call(function () {
    $goodsCon = new GoodsController;
    $response = $goodsCon->expiringGoods();
    $expiring = collect($response->getData());

    if ($expiring->count() > 0) {
        broadcast(new InventoryNotification(
            'Barang Akan Kedaluwarsa',
            'Ada ' . $expiring->count() . ' barang kadaluwarsa dalam 7 hari.'
        ));
    }
})->everyMinute();