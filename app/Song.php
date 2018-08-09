<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    public function albums()
    {
        return $this->belongsTo(Album::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }

    public function flacFile()
    {
        return $this->hasOne(FlacFile::class);
    }
}
