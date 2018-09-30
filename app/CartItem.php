<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $guarded = ['id'];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function cartable()
    {
        return $this->morphTo();
    }
}
