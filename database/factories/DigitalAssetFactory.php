<?php

namespace Database\Factories;

use App\DigitalAsset;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use IndieHD\Velkart\Contracts\Repositories\Eloquent\ProductRepositoryContract;

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
        $product = 'Samsung Galaxy S10';

        return [
            'product_id' => resolve(ProductRepositoryContract::class)->create([
                'sku'         => $this->faker->unique()->numberBetween(1111111, 999999),
                'name'        => $product,
                'slug'        => Str::slug($product),
                'description' => $this->faker->paragraph,
                'cover'       => UploadedFile::fake()->image('product.png', 600, 600),
                'quantity'    => 10,
                'price'       => 9.95,
                'status'      => 1,
            ])->id,
            'asset_id' => null, // should be overwritten on creation
            'asset_type' => null, // should be overwritten on creation
        ];
    }
}
