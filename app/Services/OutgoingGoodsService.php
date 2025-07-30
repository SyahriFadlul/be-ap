<?php

namespace App\Services;

use App\Models\OutgoingGoods;
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
                'items.*.id' => 'required|exists:goods,id',
                'items.*.batch_id' => 'required|exists:goods_batches,id',
                'items.*.qty' => 'required|numeric|min:1',
                'items.*.unit_id' => 'required|exists:units,id',
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
                    'goods_id' => $item['id'],
                    'batch_id' => $item['batch_id'],
                    'unit_id' => $item['unit_id'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'line_total' => $item['qty'] * $item['unit_price'],
                ]);                
            }

            if($item['unit_id' === 8]){ //boksa

            }

            return $outgoing;
        });
    }
}