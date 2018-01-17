<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class CatalogResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     *
     *
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'moniker' => $this->moniker,
            'alt_moniker' => $this->alt_moniker,
            'city' => $this->city,
            'territory' => $this->territory,
            'country_code' => $this->country_code,
            'country' => $this->country->name,
            'official_url' => $this->official_url,
            'profile_url' => $this->profile_url,
            'rank' => $this->rank,
            'is_active' => $this->is_active,
            'approver' => $this->approver,
            'approved_at' => $this->approved_at,
            'account' => $this->user,
            'deleter' => $this->deleter,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
