<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FlacFile extends Model
{
    public function songs()
    {
        return $this->hasMany(Song::class);
    }
}
