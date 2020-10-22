<?php

namespace Database\Factories;

use App\DigitalAsset;
use Illuminate\Database\Eloquent\Factories\Factory;
use IndieHD\Velkart\Models\Eloquent\Product;

class DigitalAssetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DigitalAsset::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'product_id' => Product::factory(),
            'asset_id' => null, // should be overwritten on creation
            'asset_type' => null // should be overwritten on creation
        ];
    }
}
