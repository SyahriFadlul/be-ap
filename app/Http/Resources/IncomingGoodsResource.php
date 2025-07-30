<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IncomingGoodsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return[
            'id' => $this->id,
            'received_date' => Carbon::parse($this->received_date)->toFormattedDateString(),
            'supplier_id' => $this->whenLoaded('supplier')->id,
            'supplier' => $this->whenLoaded('supplier')->name,
            'invoice' => $this->invoice,
            'amount' => $this->amount,
            'items' => $this->items->map(fn ($item) => [
                'id' => $item->id,
                'goods_id' => $item->relationLoaded('goods') ? new GoodsResource($item->goods)->id : null,
                'goods' => $item->relationLoaded('goods') ? new GoodsResource($item->goods)->name : null,
                'expiry_date' => $item->relationLoaded('batch') ? $item->batch->expiry_date : null,
                'batch_number' => $item->relationLoaded('batch') ? $item->batch->batch_number : null,
                'qty' => $item->qty,
                'unit_id' => $item->relationLoaded('unit') ? $item->unit->id : null,
                'unit' => $item->relationLoaded('unit') ? $item->unit->name : null,
                'conversion_qty' => $item->conversion_qty,
                'unit_price' => $item->unit_price,
                'line_total' => $item->line_total,
            ]),
            'created_by' => $this->whenLoaded('createdBy')->username,
        ];
    }
}
