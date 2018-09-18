<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
            'moniker' => $this->moniker,
            'alt_moniker' => $this->alt_moniker,
            'email' => $this->email,
            'city' => $this->city,
            'territory' => $this->territory,
            'country_code' => $this->country_code,
            'country' => $this->country->name,
            'official_url' => $this->official_url,
            'profile_url' => $this->profile_url,
            'rank' => $this->rank,
        ];
    }
}
