<?php

namespace App\Services;

use App\Models\Goods;
use App\Models\GoodsBatch;
use App\Models\IncomingGoods;
use App\Models\OutgoingGoods;
use App\Models\OutgoingGoodsItems;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OutgoingGoodsService
{
    public function store(array $data, User $user)
    {
        return DB::transaction(function () use ($data,$user){
            $validator = Validator::make($data,[
                'date' => 'required|date',
                'transc_type' => 'required|string',
                'note' => 'nullable|string',
                'amount' => 'required|numeric|min:0',
                'items' => 'required|array|min:1',
                'items.*.goods_id' => 'required|exists:goods,id',
                'items.*.batch_id' => 'required|exists:goods_batches,id',
                'items.*.qty' => 'required|numeric|min:1',
                'items.*.unit_id' => 'required|exists:units,id',
                'items.*.unit' => 'required|array',
                'items.*.unit.status' => 'required|in:base,medium,large',
                'items.*.unit_price' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            $validated = $validator->validated();

            $outgoing = OutgoingGoods::create([
                'date' => Carbon::parse($validated['date']),
                'type' => $validated['transc_type'],
                'amount' => $validated['amount'],
                'note' => $validated['note'] ?? null,
                'created_by' => $user->id,
            ]);

            foreach ($validated['items'] as $item) {
                $outgoing->items()->create([
                    'goods_id' => $item['goods_id'],
                    'batch_id' => $item['batch_id'],
                    'unit_id' => $item['unit_id'],
                    'final_qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $item['qty'] * $item['unit_price'],
                ]);
                
                $unitSize = strtolower($item['unit']['status']);
                $goods = Goods::findOrFail($item['goods_id']);
                $batch = GoodsBatch::findOrFail($item['batch_id']);

                if($unitSize === 'medium'){
                    $batch->decrement('qty', $item['qty'] * $goods->conversion_medium_to_base);
                }
                elseif($unitSize === 'large'){
                    $batch->decrement('qty', $item['qty'] * $goods->conversion_large_to_medium * $goods->conversion_medium_to_base);
                }
                else{
                    $batch->decrement('qty', $item['qty']);
                }
            }

            return $outgoing;
        });
    }

    public function update(array $data, $id, User $user)
    {
        return DB::transaction(function () use ($data, $id, $user) {
            $validator = Validator::make($data, [
                'date' => 'required|date',
                'transc_type' => 'required|string',
                'note' => 'nullable|string',
                'amount' => 'required|numeric|min:0',
                'items' => 'required|array|min:1',
                'items.*.outgoing_goods_items_id' => 'required|exists:outgoing_goods_items,id',
                'items.*.goods_id' => 'required|exists:goods,id',
                'items.*.batch_id' => 'required|exists:goods_batches,id',
                'items.*.qty' => 'required|numeric|min:1',
                'items.*.unit_id' => 'required|exists:units,id',
                'items.*.unit' => 'required|array',
                'items.*.unit.status' => 'required|in:base,medium,large',
                'items.*.unit_price' => 'required|numeric|min:0',
            ]);

            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            $validated = $validator->validated();

            $outgoing = OutgoingGoods::findOrFail($id);
            $outgoing->update([
                'date' => Carbon::parse($validated['date']),
                'type' => $validated['transc_type'],
                'amount' => $validated['amount'],
                'note' => $validated['note'] ?? null,
            ]);

            

            foreach ($validated['items'] as $item) {
                $itemModel = OutgoingGoodsItems::findOrFail($item['outgoing_goods_items_id']);
                $conversion = $itemModel->goods; // conv_med_to_base, conv_large_to_med

                $initialQty = $itemModel->final_qty; //qty yang sebelumnya
                $finalQty = $item['qty']; //qty yang baru

                $initialUnit = $itemModel->unit->status; //dari model
                $finalUnit = strtolower($item['unit']['status']); //dari request

                //konversi qty lama ke base
                if ($initialUnit === 'medium') {
                    $initialQtyInBase = $initialQty * $conversion->conversion_medium_to_base;
                } elseif ($initialUnit === 'large') {
                    $initialQtyInBase = $initialQty * $conversion->conversion_large_to_medium * $conversion->conversion_medium_to_base;
                } else {
                    $initialQtyInBase = $initialQty; // base
                }

                //konversi qty baru ke base
                if ($finalUnit === 'medium') {
                    $finalQtyInBase = $finalQty * $conversion->conversion_medium_to_base;
                } elseif ($finalUnit === 'large') {
                    $finalQtyInBase = $finalQty * $conversion->conversion_large_to_medium * $conversion->conversion_medium_to_base;
                } else {
                    $finalQtyInBase = $finalQty; // base
                }

                // Hitung selisih utk -+ stok 
                $difference = $initialQtyInBase - $finalQtyInBase;

                if ($itemModel->batch->qty < abs($difference)) {
                   throw new \Exception('Stok tidak mencukupi untuk update.');
                }

                if ($difference > 0) {                                  // selisih +, qty/stok -
                    $itemModel->batch->increment('qty', $difference);
                } elseif ($difference < 0) {                            // selisih -, qty/stok +
                    $itemModel->batch->decrement('qty', abs($difference));
                }
                            

                $itemModel->update([
                    'goods_id' => $item['goods_id'],
                    'batch_id' => $item['batch_id'],
                    'unit_id' => $item['unit_id'],
                    'final_qty' => $item['qty'],
                    'initial_qty' => $initialQty, //simpen qty lama
                    'unit_price' => $item['unit_price'],
                    'line_total' => $item['qty'] * $item['unit_price'],
                ]);
            }

            return $outgoing;
        });
    }
}