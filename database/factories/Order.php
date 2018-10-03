<?php

use Faker\Generator as Faker;

use Ramsey\Uuid\Uuid;

$factory->define(App\Order::class, function (Faker $faker) {
    return [
        'uuid' => Uuid::uuid4()->toString(),
    ];
});
