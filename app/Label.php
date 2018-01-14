<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    public function catalogable()
    {
        return $this->morphOne(CatalogEntity::class, 'catalogable');
    }
}
