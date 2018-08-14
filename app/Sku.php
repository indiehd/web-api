<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sku extends Model
{
    public function songs()
    {
        return $this->hasMany(Song::class);
    }
}
