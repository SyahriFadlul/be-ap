<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OutgoingGoodsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);

        return [
            'id' => $this->id,
            'date' => Carbon::parse($this->date)->toFormattedDateString(),
            'type' => $this->type,
            'invoice' => $this->invoice,
            'note' => $this->note,
            'amount' => $this->amount,
            'items' => $this->items->map(fn ($item) => [
                'outgoing_goods_id' => $item->id,
                'goods' => $item->relationLoaded('goods') ? new GoodsResource($item->goods)->name : null,
                'batch_number' => $item->relationLoaded('batch') ? $item->batch->batch_number : null,
                'unit' => $item->relationLoaded('unit') ? $item->unit->name : null,
                'qty' => $item->qty
            ]),
            'created_by' => $this->whenLoaded('createdBy')->username,
        ];
    }
}
