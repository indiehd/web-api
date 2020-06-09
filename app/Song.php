<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'bool',
        'is_digital' => 'bool',
        'is_taxable' => 'bool',
        'preview_start' => 'decimal:3',
        'price' => 'decimal:4',
        'requires_shipping' => 'bool',
        'track_number' => 'int',
    ];

    public function album()
    {
        return $this->belongsTo(Album::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function flacFile()
    {
        return $this->belongsTo(FlacFile::class);
    }

    public function copiesSold()
    {
        return $this->morphMany(OrderItem::class, 'orderable');
    }
}
