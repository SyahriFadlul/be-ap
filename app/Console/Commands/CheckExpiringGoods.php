<?php

namespace App\Console\Commands;

use App\Http\Controllers\GoodsController;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckExpiringGoods extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-expiring-goods';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cek barang kedaluwarsa dan stok menipis lalu simpan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $now = Carbon::now();
        // $sevenDaysLater = $now->copy()->addDays(7);
        $GoodsCon = new GoodsController();

        $expiring = $GoodsCon->expiringGoods();
        $lowStock = Goods::whereHas('batches', function ($q){
            $q->where('stock', '<', 50)->get();
            })->get();

        if ($expiring->count() > 0) {
            SystemNotification::create([
                'title' => 'Barang Kedaluwarsa',
                'message' => "Ada {$expiring->count()} barang yang akan kedaluwarsa.",
                'type' => 'warning',
            ]);
        }

        if ($lowStock->count() > 0) {
            SystemNotification::create([
                'title' => 'Stok Menipis',
                'message' => "Ada {$lowStock->count()} barang dengan stok menipis.",
                'type' => 'info',
            ]);
        }

        $this->info('Notifikasi berhasil disimpan.');
    }
}
