<?php

use Faker\Generator as Faker;

use Ramsey\Uuid\Uuid;

$factory->define(App\Cart::class, function (Faker $faker) {
    return [
        'uuid' => Uuid::uuid1(),
    ];
});
