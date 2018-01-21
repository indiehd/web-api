<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    public function profilable()
    {
        return $this->morphTo();
    }
}
