<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Artist;

class Featured extends Model
{
    public function featurable()
    {
        return $this->morphTo();
    }

    public function scopeArtists($query)
    {
        return $query->where('featurable_type', Artist::class);
    }
}
