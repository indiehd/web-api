<?php

use App\Sku;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Sku::class, function (Faker $faker) {

    $digital = 1;

    return [
        'price' => (float)(rand(0, 10).'.'.rand(0, 9).rand(0, 9)),
        'is_digital' => 1,
        'is_taxable' => 0,
        'requires_shipping' => !$digital,
        'is_active' => $faker->boolean(95)
    ];
});
