<?php

namespace App\Http\Resources;

use App\Contracts\ArtistRepositoryInterface;
use Illuminate\Http\Resources\Json\JsonResource;

class LabelResource extends JsonResource
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
            'artists' => ArtistResource::collection($this->whenLoaded('artists')),
            'albums' => AlbumResource::collection($this->whenLoaded('albums')),
            'artists_count' => $this->artists->count(),
            'albums_count' => $this->albums->count(),
            // TODO: Add songs_count and a Collection of Song Models
        ];
    }
}
