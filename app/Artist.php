<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    protected $guarded = ['id'];

    public function catalogable()
    {
        return $this->morphOne(CatalogEntity::class, 'catalogable');
    }

    public function profile()
    {
        return $this->morphOne(Profile::class, 'profilable');
    }

    public function label()
    {
        return $this->belongsTo(Label::class);
    }

    public function songs()
    {
        return $this->hasManyThrough(Song::class, Album::class);
    }

    public function albums()
    {
        return $this->hasMany(Album::class);
    }
}
