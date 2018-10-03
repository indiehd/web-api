<?php

use Faker\Generator as Faker;

$factory->define(App\OrderItem::class, function (Faker $faker) {
    return [
        'orderable_id' => null, // should be overwritten on creation
        'orderable_type' => null // should be overwritten on creation
    ];
});
