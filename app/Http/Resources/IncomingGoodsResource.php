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
            'supplier' => $this->whenLoaded('supplier')->name,
            'invoice' => $this->invoice,
            'amount' => $this->amount,
            'items' => $this->items->map(fn ($item) => [
                    'goods' => $item->relationLoaded('goods') ? new GoodsResource($item->goods)->name : null,
                    'batch_number' => $item->relationLoaded('batch') ? $item->batch->batch_number : null,
                    'qty' => $item->qty,
                    'unit' => $item->relationLoaded('unit') ? $item->unit->name : null,
                    'conversion_qty' => $item->conversion_qty,
                    'price_per_line' => $item->price_per_line,
                    'total_price' => $item->total_price,
                ]
            ),
            'created_by' => $this->whenLoaded('createdBy')->username,
        ];
    }
}
