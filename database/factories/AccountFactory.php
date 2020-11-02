<?php

namespace Database\Factories;

use App\Account;
use Faker\Provider\en_US\Address;
use Faker\Provider\en_US\Company;
use Faker\Provider\en_US\Person;
use Faker\Provider\en_US\PhoneNumber;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Account::class;

    public function configure()
    {
        $this->faker->addProvider(new Person($this->faker));
        $this->faker->addProvider(new Address($this->faker));
        $this->faker->addProvider(new PhoneNumber($this->faker));
        $this->faker->addProvider(new Company($this->faker));

        return $this;
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = $this->faker;

        $true = $faker->boolean(25);

        return [
            'email' => $faker->unique()->email,
            'first_name' => $true ? $faker->firstName : null,
            'last_name' => $true ? $faker->lastName : null,
            'address_one' => $true ? $faker->streetAddress : null,
            'address_two' => $true ? ($faker->boolean(15) ? $faker->secondaryAddress : null) : null,
            'city' => $true ? $faker->city : null,
            'territory' => $true ? $faker->state : null,
            'country_code' => $true ? 'US' : null,
            'postal_code' => $true ? $faker->postcode : null,
            'phone' => $true ? $faker->phoneNumber : null,
            'alt_phone' => $true ? ($faker->boolean(15) ?: $faker->phoneNumber) : null
        ];
    }
}
