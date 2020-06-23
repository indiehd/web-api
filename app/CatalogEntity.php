<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CatalogEntity extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'bool',
    ];

    protected $dates = [
        'approved_at',
        'deleted_at',
    ];

    protected $morphClass = 'CatalogEntity';

    public function catalogable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class);
    }

    public function deleter()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_code', 'code');
    }
}
