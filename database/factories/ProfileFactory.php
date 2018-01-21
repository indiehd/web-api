<?php

use Faker\Generator as Faker;
use Faker\Provider\en_US\Address;
use Faker\Provider\en_US\Person;
use Faker\Provider\en_US\PhoneNumber;
use Faker\Provider\en_US\Company;

$factory->define(App\Profile::class, function (Faker $faker) {
    $faker->addProvider(new Person($faker));
    $faker->addProvider(new Address($faker));
    $faker->addProvider(new PhoneNumber($faker));
    $faker->addProvider(new Company($faker));

    return [
        'moniker' => $faker->company,
        'alt_moniker' => $faker->boolean(50) ? $faker->company : null,
        'email' => $faker->boolean(50) ? $faker->email : null,
        'city' => $faker->city,
        'territory' => $faker->state,
        'country_code' => 'US',
        'official_url' => $faker->boolean(70) ? $faker->url : null,
        'profile_url' => $faker->slug,
        'rank' => $faker->boolean(80) ? $faker->numberBetween(1, 100000) : null,
        'profilable_id' => null, // should be overwritten on creation
        'profilable_type' => null // should be overwritten on creation
    ];
});
