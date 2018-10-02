<?php

use Faker\Generator as Faker;

$factory->define(App\CartItem::class, function (Faker $faker) {
    return [
        'cartable_id' => null, // should be overwritten on creation
        'cartable_type' => null // should be overwritten on creation
    ];
});
