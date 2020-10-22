<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    use HasFactory;

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

    public function albums()
    {
        return $this->hasManyThrough(Album::class, Artist::class);
    }

    public function user()
    {
        return $this->catalogable->user();
    }
}
