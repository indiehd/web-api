<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $dates = ['approved_at'];

    public function albums()
    {
        return $this->belongsToMany(Album::class)
            ->withTimestamps();
    }
}
