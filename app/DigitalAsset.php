<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use IndieHD\Velkart\Models\Eloquent\Product;

class DigitalAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'asset_id',
        'asset_type'
    ];

    protected $casts = [
        'asset_id' => 'int',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function asset()
    {
        return $this->morphTo();
    }
}
