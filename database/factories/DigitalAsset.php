<?php

use Faker\Generator as Faker;
use IndieHD\Velkart\Models\Eloquent\Product;

$factory->define(App\DigitalAsset::class, function (Faker $faker) {
    return [
        'product_id' => factory(Product::class),
        'asset_id' => null, // should be overwritten on creation
        'asset_type' => null // should be overwritten on creation
    ];
});
