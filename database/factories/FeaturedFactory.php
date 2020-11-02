<?php

namespace Database\Factories;

use App\Featured;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeaturedFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Featured::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'featurable_id' => null, // should be overridden on creation
            'featurable_type' => null, // should be overridden on creation
        ];
    }
}
