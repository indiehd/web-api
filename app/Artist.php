<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{

    public function profile()
    {
        return $this->morphOne(CatalogEntity::class, 'catalogable');
    }

    public function label()
    {
        return $this->belongsTo(Label::class);
    }
}
