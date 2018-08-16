<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public function profiles()
    {
        return $this->hasMany(Profile::class, 'country_code', 'code');
    }
}
