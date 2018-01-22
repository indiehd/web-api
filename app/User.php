<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Cartalyst\Sentinel\Users\EloquentUser;

class User extends EloquentUser
{
    use Notifiable;

    /**
     * {@inheritDoc}
     */
    protected $fillable = [
        'email',
        'username',
        'password',
        'permissions',
    ];

    /**
     * {@inheritDoc}
     */
    protected $hidden = [
        'password',
    ];

    /**
     * {@inheritDoc}
     */
    protected $loginNames = ['email'];

    public function entities()
    {
        return $this->hasMany(CatalogEntity::class);
    }

    public function purchased()
    {
        return $this->belongsToMany(Song::class);
    }
}
