<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sku extends Model
{
    public function song()
    {
        return $this->hasOne(Song::class);
    }
}
