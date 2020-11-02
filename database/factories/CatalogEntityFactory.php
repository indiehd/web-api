<?php

namespace Database\Factories;

use App\CatalogEntity;
use App\Contracts\UserRepositoryInterface;
use Faker\Provider\en_US\Address;
use Faker\Provider\en_US\Company;
use Faker\Provider\en_US\Person;
use Faker\Provider\en_US\PhoneNumber;
use Illuminate\Database\Eloquent\Factories\Factory;

class CatalogEntityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CatalogEntity::class;

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
        $user = resolve(UserRepositoryInterface::class);

        $faker = $this->faker;

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
            'approver_id' => $faker->boolean(50) ? $user->model()::inRandomOrder()->first()->id : null,
            'deleter_id' => $faker->boolean(15) ? $user->model()::inRandomOrder()->first()->id : null,
            'catalogable_id' => null, // should be overwritten on creation
            'catalogable_type' => null, // should be overwritten on creation
        ];
    }
}
