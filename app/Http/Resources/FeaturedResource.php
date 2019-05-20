<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FeaturedResource extends JsonResource
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
            'moniker' => (new ProfileResource($this->featurable->profile))->moniker,
            'songs_count' => SongResource::collection($this->featurable->songs)->count(),
            'albums_count' => AlbumResource::collection($this->featurable->albums)->count(),
            'profile_url' => (new ProfileResource($this->featurable->profile))->profile_url,
        ];
    }
}
