<?php

use Faker\Generator as Faker;
use Faker\Provider\en_US\Address;
use Faker\Provider\en_US\Person;
use Faker\Provider\en_US\PhoneNumber;
use Faker\Provider\en_US\Company;

use App\User;

$factory->define(App\CatalogEntity::class, function (Faker $faker) {
    $faker->addProvider(new Person($faker));
    $faker->addProvider(new Address($faker));
    $faker->addProvider(new PhoneNumber($faker));
    $faker->addProvider(new Company($faker));

    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->boolean(50) ? $faker->email : null,
        'address_one' => $faker->streetAddress,
        'address_two' => $faker->boolean(25) ? $faker->secondaryAddress : null,
        'city' => $faker->city,
        'territory' => $faker->state,
        'country_code' => 'US',
        'postal_code' => $faker->postcode,
        'phone' => $faker->phoneNumber,
        'alt_phone' => $faker->boolean(25) ? $faker->phoneNumber : null,
        'is_active' => $faker->boolean(50),
        'user_id' => null, // should be overwritten on creation
        'approver_id' => 1,
        'deleter_id' => 1,
        'catalogable_id' => null, // should be overwritten on creation
        'catalogable_type' => null // should be overwritten on creation
    ];
});
