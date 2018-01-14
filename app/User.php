<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function catalogable()
    {
        return $this->morphOne(CatalogEntity::class, 'catalogable');
    }

    public function fan()
    {
        return $this->hasOne(Fan::class);
    }

    public function songs()
    {
        return $this->catalogable ? $this->catalogable->songs : collect([]);
    }

    public function purchased()
    {
        return $this->belongsToMany(Song::class);
    }
}
