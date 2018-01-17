<?php

use Faker\Generator as Faker;
use Faker\Provider\en_US\Address;
use Faker\Provider\en_US\Person;
use Faker\Provider\en_US\PhoneNumber;
use Faker\Provider\en_US\Company;

$factory->define(App\Fan::class, function (Faker $faker) {
    $faker->addProvider(new Person($faker));
    $faker->addProvider(new Address($faker));
    $faker->addProvider(new PhoneNumber($faker));
    $faker->addProvider(new Company($faker));

    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->email,
        'address_one' => $faker->streetAddress,
        'address_two' => $faker->boolean(25) ? $faker->secondaryAddress : null,
        'city' => $faker->city,
        'territory' => $faker->state,
        'country_code' => 'US',
        'postal_code' => $faker->postcode,
        'phone' => $faker->phoneNumber,
        'alt_phone' => $faker->boolean(25) ? $faker->phoneNumber : null,
        'user_id' => function () {
            return factory(App\User::class)->create()->id;
        }
    ];
});
