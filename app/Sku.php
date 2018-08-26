<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sku extends Model
{
    protected $guarded = ['id'];

    public function songs()
    {
        return $this->hasMany(Song::class);
    }
}
