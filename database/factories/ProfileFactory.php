<?php

namespace Database\Factories;

use App\Profile;
use Faker\Provider\en_US\Address;
use Faker\Provider\en_US\Company;
use Faker\Provider\en_US\Person;
use Faker\Provider\en_US\PhoneNumber;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Profile::class;

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

        return [
            'moniker' => $faker->company,
            'alt_moniker' => $faker->boolean(50) ? $faker->company : null,
            'email' => $faker->boolean(50) ? $faker->email : null,
            'city' => $faker->city,
            'territory' => $faker->state,
            'country_code' => 'US',
            'official_url' => $faker->boolean(70) ? $faker->url : null,
            'profile_url' => trim(substr($faker->slug(), 0, rand(1, 64)), '-'), // ensure reasonable length and no resultant leading/trailing dashes
            'rank' => $faker->boolean(80) ? $faker->numberBetween(1, 100000) : null,
            'profilable_id' => null, // should be overwritten on creation
            'profilable_type' => null, // should be overwritten on creation
        ];
    }
}
