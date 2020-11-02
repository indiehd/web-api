<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SkuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'price' => $this->price,
            'description' => $this->description,
            'is_digital' => $this->is_digital,
            'is_taxable' => $this->is_taxable,
            'requires_shipping' => $this->requires_shipping,
            'is_active' => $this->is_active,
            'songs' => SongResource::collection($this->songs),
        ];
    }
}
