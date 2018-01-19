<?php

use Faker\Generator as Faker;
use Faker\Provider\en_US\Address;
use Faker\Provider\en_US\Person;
use Faker\Provider\en_US\PhoneNumber;
use Faker\Provider\en_US\Company;

$factory->define(App\User::class, function (Faker $faker) {
    $faker->addProvider(new Person($faker));
    $faker->addProvider(new Address($faker));
    $faker->addProvider(new PhoneNumber($faker));
    $faker->addProvider(new Company($faker));

    $true = $faker->boolean(25);

    return [
        'email' => $faker->unique()->email,
        'username' => $faker->unique()->userName,
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'first_name' => $true ? $faker->firstName : null,
        'last_name' => $true ? $faker->lastName : null,
        'address_one' => $true ? $faker->streetAddress : null,
        'address_two' => $true ? $faker->boolean(15) ? $faker->secondaryAddress : null : null,
        'city' => $true ? $faker->city : null,
        'territory' => $true ? $faker->state : null,
        'country_code' => $true ? 'US' : null,
        'postal_code' => $true ? $faker->postcode : null,
        'phone' => $true ? $faker->phoneNumber : null,
        'alt_phone' => $true ? $faker->boolean(15) ?: $faker->phoneNumber : null
    ];
});
