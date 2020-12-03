<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use IndieHD\Velkart\Models\Eloquent\OrderStatus;

class OrderStatusFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrderStatus::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
        ];
    }
}
