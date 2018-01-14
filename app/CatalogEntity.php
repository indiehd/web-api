<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CatalogEntity extends Model
{
    protected $morphClass = 'CatalogEntity';

    public function catalogable()
    {
        return $this->morphTo();
    }
}
