<?php

namespace Database\Factories;

use App\Contracts\DigitalAssetRepositoryInterface;
use App\Contracts\FlacFileRepositoryInterface;
use App\Song;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use IndieHD\Velkart\Contracts\Repositories\Eloquent\ProductRepositoryContract;

class SongFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Song::class;

    public function configure()
    {
        $this->afterCreating(function (Song $song) {
            $song->asset()->save(static::factoryForModel(resolve(DigitalAssetRepositoryInterface::class)->class())->make([
                'product_id' => resolve(ProductRepositoryContract::class)->create([
                    'name' => $song->name,
                    'slug' => Str::slug($song->name),
                    'description' => null,
                    'price' => '10', // TODO Represent this with Money\Money via $song->price.
                    'sku'         => $this->faker->unique()->numberBetween(1111111, 999999),
                    'cover'       => UploadedFile::fake()->image('product.png', 600, 600),
                    'quantity'    => 10,
                    'status'      => 1,
                ])->id,
                'asset_id' => $song->id,
                'asset_type' => App\Song::class,
            ]));
        });

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
            'name' => $faker->company,
            'alt_name' => $faker->company,
            'flac_file_id' => static::factoryForModel(resolve(FlacFileRepositoryInterface::class)->class()),
            'track_number' => null, // passed during creation
            'preview_start' => $faker->numberBetween(0, 60),
            'price' =>  $faker->numberBetween(0, 1000),
            'is_digital' => 1,
            'is_taxable' => 0,
            'requires_shipping' => false,
            'is_active' => $faker->boolean(85),
            'album_id' => null, // passed during creation
            //'is_in_back_catalog' => $faker->boolean(95),
        ];
    }
}
