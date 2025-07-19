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
                'goods_id' => 5,
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
                'goods_id' => 6,
                'batch_number' => '826712',
                'qty' => 30,
                'selling_price' => 5000,
                'purchase_price' => 2800,
                'expiry_date' => Carbon::create(2030, 4, 30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'incoming_goods_item_id' => 4,
                'goods_id' => 16,
                'batch_number' => '2746187',
                'qty' => 20,
                'selling_price' => 5000,
                'purchase_price' => 2300,
                'expiry_date' => Carbon::create(2030, 5, 30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'incoming_goods_item_id' => 5,
                'goods_id' => 9,
                'batch_number' => '476187',
                'qty' => 20,
                'selling_price' => 8500,
                'purchase_price' => 7000,
                'expiry_date' => Carbon::create(2030, 6, 30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'incoming_goods_item_id' => 6,
                'goods_id' => 22,
                'batch_number' => '987135',
                'qty' => 20,
                'selling_price' => 4000,
                'purchase_price' => 2550,
                'expiry_date' => Carbon::create(2030, 7, 30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'incoming_goods_item_id' => 7,
                'goods_id' => 17,
                'batch_number' => '65651',
                'qty' => 6,
                'selling_price' => 26000,
                'purchase_price' => 15500,
                'expiry_date' => Carbon::create(2030, 8, 30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'incoming_goods_item_id' => 8,
                'goods_id' => 25,
                'batch_number' => '7891',
                'qty' => 20,
                'selling_price' => 2000,
                'purchase_price' => 925,
                'expiry_date' => Carbon::create(2030, 9, 30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'incoming_goods_item_id' => 9,
                'goods_id' => 27,
                'batch_number' => '1486',
                'qty' => 6,
                'selling_price' => 28000,
                'purchase_price' => 21600,
                'expiry_date' => Carbon::create(2030, 10, 30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'incoming_goods_item_id' => 10,
                'goods_id' => 29,
                'batch_number' => '6841',
                'qty' => 20,
                'selling_price' => 10000,
                'purchase_price' => 8000,
                'expiry_date' => Carbon::create(2030, 11, 30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'incoming_goods_item_id' => 11,
                'goods_id' => 23,
                'batch_number' => '3654',
                'qty' => 20,
                'selling_price' => 2500,
                'purchase_price' => 980,
                'expiry_date' => Carbon::create(2030, 12, 30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'incoming_goods_item_id' => 12,
                'goods_id' => 20,
                'batch_number' => '794531',
                'qty' => 20,
                'selling_price' => 5000,
                'purchase_price' => 3480,
                'expiry_date' => Carbon::create(2031, 1, 30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'incoming_goods_item_id' => 13,
                'goods_id' => 10,
                'batch_number' => '30950',
                'qty' => 20,
                'selling_price' => 4500,
                'purchase_price' => 3450,
                'expiry_date' => Carbon::create(2031, 2, 30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'incoming_goods_item_id' => 14,
                'goods_id' => 4,
                'batch_number' => '69781',
                'qty' => 20,
                'selling_price' => 3000,
                'purchase_price' => 1650,
                'expiry_date' => Carbon::create(2031, 3, 30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'incoming_goods_item_id' => 15,
                'goods_id' => 9,
                'batch_number' => '874561',
                'qty' => 20,
                'selling_price' => 25000,
                'purchase_price' => 19230,
                'expiry_date' => Carbon::create(2031, 4, 30),
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
                'goods_id' => 6, //alphamol
                'qty' => 2,
                'unit_id' => 8,
                'conversion_qty' => 15,
                'price_per_line' => 42000,
                'total_price' => 84000,
            ],
        ]);
        IncomingGoods::create([
            'supplier_id' => 2,
            'invoice' => 'INV-2501002',
            'received_date' => Carbon::create(2024,7,1),
            'amount' => 46000,
            'created_by' => 1,
            ])
        ->items()->create([
                'goods_id' => 16, // asam mafenamat
                'qty' => 2,
                'unit_id' => 8,
                'conversion_qty' => 10, 
                'price_per_line' => 23000,
                'total_price' => 46000,
        ]);
        IncomingGoods::create([
            'supplier_id' => 3,
            'invoice' => 'INV-2501003',
            'received_date' => Carbon::create(2024,8,1),
            'amount' => 140000,
            'created_by' => 1,
            ])
        ->items()->create([
                'goods_id' => 9, // amoxiclin
                'qty' => 2,
                'unit_id' => 8,
                'conversion_qty' => 10, 
                'price_per_line' => 70000,
                'total_price' => 140000,
        ]);
        IncomingGoods::create([
            'supplier_id' => 1,
            'invoice' => 'INV-2501004',
            'received_date' => Carbon::create(2024,9,1),
            'amount' => 51000,
            'created_by' => 1,
            ])
        ->items()->create([
                'goods_id' => 22, 
                'qty' => 2,
                'unit_id' => 8,
                'conversion_qty' => 10, 
                'price_per_line' => 25500,
                'total_price' => 51000,
        ]);
        IncomingGoods::create([
            'supplier_id' => 2,
            'invoice' => 'INV-2501005',
            'received_date' => Carbon::create(2024,10,1),
            'amount' => 93000,
            'created_by' => 1,
            ])
        ->items()->create([
                'goods_id' => 17, 
                'qty' => 2,
                'unit_id' => 8,
                'conversion_qty' => 3, 
                'price_per_line' => 46500,
                'total_price' => 93000,
        ]);
        IncomingGoods::create([
            'supplier_id' => 3,
            'invoice' => 'INV-2501006',
            'received_date' => Carbon::create(2024,11,1),
            'amount' => 18500,
            'created_by' => 1,
            ])
        ->items()->create([
                'goods_id' => 25, 
                'qty' => 2,
                'unit_id' => 8,
                'conversion_qty' => 10, 
                'price_per_line' => 9250,
                'total_price' => 18500,
        ]);
        IncomingGoods::create([
            'supplier_id' => 1,
            'invoice' => 'INV-2501007',
            'received_date' => Carbon::create(2024,12,1),
            'amount' => 129600,
            'created_by' => 1,
            ])
        ->items()->create([
                'goods_id' => 27, 
                'qty' => 2,
                'unit_id' => 8,
                'conversion_qty' => 3, 
                'price_per_line' => 64800,
                'total_price' => 129600,
        ]);
        IncomingGoods::create([
            'supplier_id' => 2,
            'invoice' => 'INV-2501008',
            'received_date' => Carbon::create(2025,1,1),
            'amount' => 160000,
            'created_by' => 1,
            ])
        ->items()->create([
                'goods_id' => 29, 
                'qty' => 2,
                'unit_id' => 8,
                'conversion_qty' => 10, 
                'price_per_line' => 80000,
                'total_price' => 160000,
        ]);
        IncomingGoods::create([
            'supplier_id' => 3,
            'invoice' => 'INV-2501009',
            'received_date' => Carbon::create(2025,2,1),
            'amount' => 19600,
            'created_by' => 1,
            ])
        ->items()->create([
                'goods_id' => 23, 
                'qty' => 2,
                'unit_id' => 8,
                'conversion_qty' => 10, 
                'price_per_line' => 9800,
                'total_price' => 19600,
        ]);
        IncomingGoods::create([
            'supplier_id' => 1,
            'invoice' => 'INV-2501010',
            'received_date' => Carbon::create(2025,3,1),
            'amount' => 69600,
            'created_by' => 1,
            ])
        ->items()->create([
                'goods_id' => 20, 
                'qty' => 2,
                'unit_id' => 8,
                'conversion_qty' => 10, 
                'price_per_line' => 34800,
                'total_price' => 69600,
        ]);
        IncomingGoods::create([
            'supplier_id' => 2,
            'invoice' => 'INV-2501011',
            'received_date' => Carbon::create(2025,4,1),
            'amount' => 69000,
            'created_by' => 1,
            ])
        ->items()->create([
                'goods_id' => 10, 
                'qty' => 2,
                'unit_id' => 8,
                'conversion_qty' => 10, 
                'price_per_line' => 34500,
                'total_price' => 69000,
        ]);
        IncomingGoods::create([
            'supplier_id' => 3,
            'invoice' => 'INV-2501012',
            'received_date' => Carbon::create(2025,5,1),
            'amount' => 33000,
            'created_by' => 1,
            ])
        ->items()->create([
                'goods_id' => 4, 
                'qty' => 2,
                'unit_id' => 8,
                'conversion_qty' => 10, 
                'price_per_line' => 16500,
                'total_price' => 33000,
        ]);
        IncomingGoods::create([
            'supplier_id' => 1,
            'invoice' => 'INV-2501013',
            'received_date' => Carbon::create(2025,6,1),
            'amount' => 384600,
            'created_by' => 1,
            ])
        ->items()->create([
                'goods_id' => 9, 
                'qty' => 2,
                'unit_id' => 8,
                'conversion_qty' => 10, 
                'price_per_line' => 192300,
                'total_price' => 384600,
        ]);
        
        GoodsBatch::insert($dataBatch);
    }
}
