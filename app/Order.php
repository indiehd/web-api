<?php

namespace App;

use IndieHD\Velkart\Models\Eloquent\Order as VelkartOrder;

class Order extends VelkartOrder
{
    protected $casts = [
        'customer_id' => 'int',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
