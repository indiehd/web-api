<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;
use IndieHD\Velkart\Models\Eloquent\Cart;
use Ramsey\Uuid\UuidFactoryInterface;

class CartFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Cart::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $identifier = resolve(UuidFactoryInterface::class)->uuid4();

        return [
            'identifier' => $identifier->toString(),
            'instance'   => 'default',
            'content'    => serialize(new Collection()),
        ];
    }
}
