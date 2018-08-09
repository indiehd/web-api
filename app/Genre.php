<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    public function albums()
    {
        return $this->belongsToMany(Album::class)
            ->withTimestamps();
    }
}
