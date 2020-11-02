<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /*$table->string('code', 3)->unique();
            $table->string('name');
            $table->boolean('us_state_dept_recognized')->default(false);
            $table->boolean('pay_pal_supported')->default(false);*/
        return [
            'id' => $this->id,
            'name' => $this->name,
            'us_state_dept_recognized' => $this->us_state_dept_recognized,
            'pay_pal_supported' => $this->pay_pal_supported,
            'accounts' => AccountResource::collection($this->whenLoaded('accounts')),
            'profiles' => ProfileResource::collection($this->whenLoaded('profiles')),
            'catalog_entities' => CatalogResource::collection($this->whenLoaded('catalog_entities')),
        ];
    }
}
