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
            'label' => new LabelResource($this->whenLoaded('label')),
            'profile' => new ProfileResource($this->whenLoaded('profile')),
            'songs' => SongResource::collection($this->whenLoaded('songs')),
            'albums' => AlbumResource::collection($this->whenLoaded('albums')),
        ];
    }
}
