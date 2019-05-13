<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use Faker\Generator as Faker;

use App\Contracts\FeaturedRepositoryInterface;

$repo = resolve(FeaturedRepositoryInterface::class);

$factory->define($repo->class(), function (Faker $faker) {
    return [
        'featurable_id' => null, // should be overridden on creation
        'featurable_type' => null // should be overridden on creation
    ];
});
