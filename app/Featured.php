<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Contracts\ArtistRepositoryInterface;

class Featured extends Model
{
    protected $fillable = [
        'is_active',
        'featurable_id',
        'featurable_type',
    ];

    public function featurable()
    {
        return $this->morphTo();
    }

    public function scopeArtists($query)
    {
        $artist = resolve(ArtistRepositoryInterface::class);

        return $query->where('featurable_type', $artist->class())
            ->where('is_active', true);
    }
}
