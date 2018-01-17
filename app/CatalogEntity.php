<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CatalogEntity extends Model
{
    protected $morphClass = 'CatalogEntity';

    public function catalogable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class);
    }

    public function deleter()
    {
        return $this->belongsTo(User::class);
    }
}
