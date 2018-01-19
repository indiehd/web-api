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

    $approver_id = $faker->boolean(50) ?: $faker->numberBetween(1, App\User::count());
    $deleter_id = $faker->boolean(15) ?: $faker->numberBetween(1, App\User::count());

    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->boolean(50) ?: $faker->email,
        'address_one' => $faker->streetAddress,
        'address_two' => $faker->boolean(25) ?: $faker->secondaryAddress,
        'city' => $faker->city,
        'territory' => $faker->state,
        'country_code' => 'US',
        'postal_code' => $faker->postcode,
        'phone' => $faker->phoneNumber,
        'alt_phone' => $faker->boolean(25) ?: $faker->phoneNumber,
        'is_active' => $faker->boolean(50),
        'user_id' => null, // should be overwritten on creation
        'approver_id' => $approver_id,
        'deleter_id' => $deleter_id,
        'catalogable_id' => null, // should be overwritten on creation
        'catalogable_type' => null // should be overwritten on creation

    ];
});