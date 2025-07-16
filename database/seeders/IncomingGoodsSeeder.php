<?php

namespace Database\Seeders;

use App\Models\GoodsBatch;
use App\Models\IncomingGoods;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IncomingGoodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        $dataBatch = [
            [
                'incoming_goods_item_id' => 1,
                'goods_id' => 3,
                'batch_number' => '72816',
                'qty' => 30, //strip (2*15)
                'selling_price' => 8000,
                'purchase_price' => 6300,
                'expiry_date' => Carbon::create(2025, 7, 31),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'incoming_goods_item_id' => 2,
                'goods_id' => 2,
                'batch_number' => '627134',
                'qty' => 25,
                'selling_price' => 8500,
                'purchase_price' => 6530,
                'expiry_date' => Carbon::create(2030, 2, 25),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'incoming_goods_item_id' => 3,
                'goods_id' => 7,
                'batch_number' => '826712',
                'qty' => 30,
                'selling_price' => 5000,
                'purchase_price' => 2800,
                'expiry_date' => Carbon::create(2030, 4, 30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        IncomingGoods::create([
            'supplier_id' => 1,
            'invoice' => 'INV-2501001',
            'received_date' => Carbon::create(2024, 1, 1),
            'amount' => 436500,
            'created_by' => 1,
            ])
        ->items()->createMany([
            [
                'goods_id' => 5, // alphara tablet
                'qty' => 2,
                'unit_id' => 8,
                'conversion_qty' => 15, // strip
                'price_per_line' => 94500,
                'total_price' => 189000,
            ],
            [
                'goods_id' => 2, //acyclovir
                'qty' => 5,
                'unit_id' => 8,
                'conversion_qty' => 5, //strip/blister
                'price_per_line' => 32650,
                'total_price' => 163250,
            ],
            [
                'goods_id' => 7, //alphamol
                'qty' => 2,
                'unit_id' => 8,
                'conversion_qty' => 15,
                'price_per_line' => 42000,
                'total_price' => 84000,
            ],
        ]);
        
        GoodsBatch::insert($dataBatch);
    }
}
