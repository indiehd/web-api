<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArtistResource extends JsonResource
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
            'label' => new LabelResource($this->label),
            'profile' => new ProfileResource($this->profile),
            'catalog_profile' => new CatalogResource($this->whenLoaded('catalogable')),
            'songs' => SongResource::collection($this->songs),
            'albums' => AlbumResource::collection($this->albums),
        ];
    }
}
