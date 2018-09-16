<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AlbumResource extends JsonResource
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
            'artist' => new ArtistResource($this->whenLoaded('artist')),
            'title' => $this->title,
            'alt_title' => $this->alt_title,
            'year' => $this->year,
            'description' => $this->description,
            'has_explicit_lyrics' => $this->has_explicit_lyrics,
            'full_album_price' => $this->full_album_price,
            'rank' => $this->rank,
            'is_active' => $this->is_active,
            'deleter' => new UserResource($this->deleter),
            'deleted_at' => $this->deleted_at,
        ];
    }
}
