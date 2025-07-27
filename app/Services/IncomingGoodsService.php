<?php

namespace App\Services;

use App\Models\IncomingGoods;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class IncomingGoodsService
{
    public function store(array $data, User $user)
    {
        return DB::transaction(function () use ($data,$user){
            $validator = Validator::make($data,[
                'supplier_id' => 'required|exists:suppliers,id',
                'received_date' => 'required|date',
                'invoice' => 'required',
                'amount' => 'required|numeric|min:0',
                'items' => 'required|array|min:1',
                'items.*.id' => 'required|exists:goods,id',
                'items.*.qty' => 'required|numeric|min:1',
                'items.*.unit_id' => 'required|exists:units,id',
                'items.*.price_per_line' => 'required|numeric|min:0',
                'items.*.batch_number' => 'required|string',
                'items.*.expiry_date' => 'required|date|after:today',
                'items.*.conversion_qty' => 'required|numeric|min:1',
            ]);

            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            $validated = $validator->validated();

            $incoming = IncomingGoods::create([
                'supplier_id' => $validated['supplier_id'],
                'received_date' => Carbon::parse($validated['received_date']),
                'invoice' => $validated['invoice'] ?? null,
                'amount' => $validated['amount'],
                'created_by' => $user->id,
            ]);

            foreach ($validated['items'] as $item) {
                $itemModel = $incoming->items()->create([
                    'goods_id' => $item['id'],
                    'unit_id' => $item['unit_id'],
                    'qty' => $item['qty'],
                    'conversion_qty' => $item['conversion_qty'],
                    'price_per_line' => $item['price_per_line'],
                    'total_price' => $item['qty'] * $item['price_per_line'],
                ]);

                $purchase_price = $item['price_per_line'] / $item['conversion_qty'];
                $selling_price = round(($purchase_price * 1.3) / 500) * 500; //keuntungan 30% dibulatkan ke 500 trdkat
                $qty = $item['qty'] * $item['conversion_qty']; // misal 1 boks isi 10 strip,qty=20

                $itemModel->batch()->create([
                    'goods_id' => $item['id'],
                    'batch_number' => $item['batch_number'],
                    'expiry_date' => Carbon::parse($item['expiry_date']),
                    'selling_price' => $selling_price,
                    'purchase_price' => $purchase_price,
                    'qty' => $qty,
                ]);
            }

            return $incoming;
        });
    }
}