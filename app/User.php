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
    protected $loginNames = ['username'];

    public function entity()
    {
        return $this->fan ? $this->fan : $this->hasOne(CatalogEntity::class);
    }

    public function purchased()
    {
        return $this->belongsToMany(Song::class);
    }

    public function fan()
    {
        return $this->hasOne(Fan::class);
    }
}
