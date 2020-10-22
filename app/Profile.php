<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'rank' => 'int',
    ];

    public function profilable()
    {
        return $this->morphTo();
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_code', 'code');
    }
}
