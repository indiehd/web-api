<?php

use Faker\Generator as Faker;

$factory->define(App\Artist::class, function (Faker $faker) {
    return [

    ];
});

$factory->state(App\Artist::class, 'onLabel', [
    'label_id' => function() {
        return factory(App\Label::class)->create()->id;
    },
]);
