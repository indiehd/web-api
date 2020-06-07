<?php

namespace App;

class Order extends UuidModel
{
    protected $guarded = ['id'];

    protected $casts = [
        'user_id' => 'int',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
