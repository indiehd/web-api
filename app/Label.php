<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    public function profile()
    {
        return $this->morphOne(CatalogEntity::class, 'catalogable');
    }

    public function artist()
    {
        return $this->hasMany(Artist::class);
    }
}
