<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    public function catalogable()
    {
        return $this->morphOne(CatalogEntity::class, 'catalogable');
    }

    public function profile()
    {
        return $this->morphOne(Profile::class, 'profilable');
    }

    public function artists()
    {
        return $this->hasMany(Artist::class);
    }
}
