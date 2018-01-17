<?php

use Faker\Generator as Faker;
use Faker\Provider\en_US\Address;
use Faker\Provider\en_US\Person;
use Faker\Provider\en_US\PhoneNumber;
use Faker\Provider\en_US\Company;

$factory->define(App\CatalogEntity::class, function (Faker $faker) {
    $faker->addProvider(new Person($faker));
    $faker->addProvider(new Address($faker));
    $faker->addProvider(new PhoneNumber($faker));
    $faker->addProvider(new Company($faker));

    $approver_id = $faker->boolean(50) ? $faker->numberBetween(1, App\User::count()) : null;
    $deleter_id = $faker->boolean(15) ? $faker->numberBetween(1, App\User::count()) : null;

    return [
        'moniker' => $faker->company,
        'alt_moniker' => $faker->boolean(50) ? $faker->company : null,
        'email' => $faker->email,
        'city' => $faker->city,
        'territory' => $faker->state,
        'country_code' => 'US',
        'official_url' => $faker->url,
        'profile_url' => $faker->slug,
        'rank' => $faker->numberBetween(0, 100000),
        'is_active' => $faker->boolean(50),
        'user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'approver_id' => $approver_id,
        'deleter_id' => $deleter_id,
        'catalogable_id' => null, // should be overwritten on creation
        'catalogable_type' => null // should be overwritten on creation

    ];
});