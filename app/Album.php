<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    protected $guarded = ['id'];

    public function songs()
    {
        return $this->hasMany(Song::class);
    }

    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class)
            ->withTimestamps();
    }

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleter_id');
    }

    public function copiesSold()
    {
        return $this->morphMany(OrderItem::class, 'orderable');
    }
}
