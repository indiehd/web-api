<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SongResource extends JsonResource
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
            'id'=> $this->id,
            'name' => $this->name,
            'alt_name' => $this->alt_name,
            'flac_file' => new FlacFileResource($this->flacFile),
            'track_number' => $this->track_number,
            'preview_start' => $this->preview_start,
            'is_active' => $this->is_active,
            'album' => new AlbumResource($this->whenLoaded('album')),
            'deleted_at' => $this->deleted_at,
            'artist' => new ArtistResource($this->whenLoaded('artist')),
        ];
    }
}
