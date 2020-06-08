<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'orderable_id',
        'orderable_type'
    ];

    protected $casts = [
        'orderable_id' => 'int',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderable()
    {
        return $this->morphTo();
    }
}
