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

$factory->afterCreating(App\Artist::class, function ($artist, $faker) {
    factory(App\CatalogEntity::class)->create([
        'user_id' => factory(App\User::class)->create()->id,
        'catalogable_id' => $artist->id,
        'catalogable_type' => App\Artist::class
    ]);
});
