<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GoodsResource extends JsonResource
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
            'name' => $this->name,

            $this->mergeWhen($request->routeIs('goods.show', 'goods.index'), [
                'category_id' => new CategoryResource($this->whenLoaded('category'))->id,
                'category' => new CategoryResource($this->whenLoaded('category'))->name,
                'base_unit_id' => $this->whenLoaded('baseUnit', fn () => $this->baseUnit->id),
                'base_unit' => $this->whenLoaded('baseUnit', fn () => $this->baseUnit->name),
                'medium_unit_id' => $this->whenLoaded('mediumUnit', fn () => $this->mediumUnit->id),
                'medium_unit' => $this->whenLoaded('mediumUnit', fn () => $this->mediumUnit->name),
                'large_unit_id' => $this->whenLoaded('largeUnit', fn () => $this->largeUnit->id),
                'large_unit' => $this->whenLoaded('largeUnit', fn () => $this->largeUnit->name),
                'conversion_medium_to_base' => $this->conversion_medium_to_base,
                'conversion_large_to_medium' => $this->conversion_large_to_medium,
                'qty' => $this->whenLoaded('batches')->sum('qty'),
                'shelf_location' => $this->shelf_location,
                'total_goods' => $this->count(),
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ]),
        ];
    }
}
