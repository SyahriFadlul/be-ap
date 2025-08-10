<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
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
            'company_name' => $this->company_name ?? $this->contact_person_name,
            
            $this->mergeWhen($request->routeIs('supplier.show', 'supplier.index'), [
                // 'company_name' => $this->company_name,
                'company_phone' => $this->company_phone,
                'contact_person_name' => $this->contact_person_name,
                'contact_person_phone' => $this->contact_person_phone,
                'note' => $this->note,
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ]),
        ];
    }
}
