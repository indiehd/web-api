<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class UserResource extends Resource
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
            'username' => $this->username,
            'last_login' => $this->last_login,
            'profile' => $this->profile(),
            'created_at' => $this->created_at
        ];
    }

    private function profile()
    {
        return $this->fan ? $this->fan : new CatalogResource($this->entity);
    }
}
