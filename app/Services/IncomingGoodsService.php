<?php

namespace App\Services;

use App\Models\Goods;
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
                'invoice' => 'required|unique:incoming_goods,invoice',
                'amount' => 'required|numeric|min:0',
                'items' => 'required|array|min:1',
                'items.*.goods_id' => 'required|exists:goods,id',
                'items.*.qty' => 'required|numeric|min:1',
                'items.*.unit_id' => 'required|exists:units,id',
                'items.*.unit' => 'required|array',
                'items.*.unit.*.status' => 'required|in:base,medium,large',
                'items.*.unit_price' => 'required|numeric|min:0',
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
                    'goods_id' => $item['goods_id'],
                    'unit_id' => $item['unit_id'],
                    'final_qty' => $item['qty'],
                    'conversion_qty' => $item['conversion_qty'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $item['qty'] * $item['unit_price'],
                ]);

                $baseConversion = Goods::findOrFail($item['goods_id'])->conversion_medium_to_base ?? 1;

                $purchase_price = $item['unit_price'] / $item['conversion_qty'];
                $selling_price = round(($purchase_price * 1.3) / 500) * 500; //keuntungan 30% dibulatkan ke 500 trdkat
                $qty = $item['qty'] * $item['conversion_qty'] * $baseConversion; // misal 1 boks isi 10 strip * base conv,

                $itemModel->batch()->create([
                    'goods_id' => $item['goods_id'],
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

    public function update(array $data, int $id, User $user)
    {
        return DB::transaction(function () use ($data, $id, $user) {
            $validator = Validator::make($data, [
                'supplier_id' => 'required|exists:suppliers,id',
                'received_date' => 'required|date',
                'invoice' => 'required|unique:incoming_goods,invoice,' . $id,
                'amount' => 'required|numeric|min:0',
                'items' => 'required|array|min:1',
                'items.*.id' => 'required|exists:incoming_goods_items,id',
                'items.*.goods_id' => 'required|exists:goods,id',
                'items.*.qty' => 'required|numeric|min:1',
                'items.*.unit_id' => 'required|exists:units,id',
                'items.*.unit_price' => 'required|numeric|min:0',
                'items.*.batch_number' => 'required|string',
                'items.*.expiry_date' => 'required|date|after:today',
                'items.*.conversion_qty' => 'required|numeric|min:1',
            ],[
                'invoice.unique' => 'Invoice sudah digunakan',
            ]);

            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            $validated = $validator->validated();

            $incoming = IncomingGoods::findOrFail($id);
            $incoming->update([
                'supplier_id' => $validated['supplier_id'],
                'received_date' => Carbon::parse($validated['received_date']),
                'invoice' => $validated['invoice'] ?? null,
                'amount' => $validated['amount'],
                'created_by' => $user->id,
            ]);

            // Update items
            foreach ($validated['items'] as $item) {
                $itemModel = $incoming->items()->findOrFail($item['id']);
                $itemModel->update([
                    'goods_id' => $item['goods_id'],
                    'unit_id' => $item['unit_id'],
                    'qty' => $item['qty'],
                    'conversion_qty' => $item['conversion_qty'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $item['qty'] * $item['unit_price'],
                ]);

                $baseConversion = Goods::findOrFail($item['goods_id'])->conversion_medium_to_base ?? 1;

                $purchase_price = $item['unit_price'] / $item['conversion_qty'];
                $selling_price = round(($purchase_price * 1.3) / 500) * 500; //keuntungan 30% dibulatkan ke 500 trdkat
                $qty = $item['qty'] * $item['conversion_qty'] * $baseConversion; // misal 1 boks isi 10 strip * base conv

                $itemModel->batch()->update([
                    'goods_id' => $item['goods_id'],
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