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
            'moniker' => $this->profile->moniker,
            'alt_moniker' => $this->profile->alt_moniker,
            'city' => $this->profile->city,
            'territory' => $this->profile->territory,
            'country_code' => $this->profile->country_code,
            'country' => $this->profile->country->name,
            'official_url' => $this->profile->official_url,
            'profile_url' => $this->profile->profile_url,
            'rank' => $this->profile->rank,
        ];
    }
}
