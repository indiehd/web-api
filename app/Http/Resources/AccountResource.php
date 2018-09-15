<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
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
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'address_one' => $this->address_one,
            'address_two' => $this->address_two,
            'city' => $this->city,
            'territory' => $this->territory,
            'country_code' => $this->country_code,
            'country' => is_null($this->country) ? null : $this->country,
            'postal_code' => $this->postal_code,
            'phone' => $this->phone,
            'alt_phone' => $this->alt_phone,
            'user' => new UserResource($this->user),
        ];
    }
}
