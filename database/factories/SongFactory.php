<?php

namespace Database\Factories;

use App\DigitalAsset;
use App\FlacFile;
use App\Song;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use IndieHD\Velkart\Models\Eloquent\Product;

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
            $song->asset()->save(DigitalAsset::factory()->make([
                'product_id' => Product::factory()->create([
                    'name' => $song->name,
                    'slug' => Str::slug($song->name),
                    'description' => null,
                    'price' => '10', // TODO Represent this with Money\Money via $song->price.
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
            'flac_file_id' => function () {
                return FlacFile::factory()->create()->id;
            },
            'track_number' => null, // passed during creation
            'preview_start' => $faker->numberBetween(0, 60),
            'price' =>  $faker->numberBetween(0, 1000),
            'is_digital' => 1,
            'is_taxable' => 0,
            'requires_shipping' => false,
            'is_active' => $faker->boolean(85),
            'album_id' => null, // passed during creation
            #'is_in_back_catalog' => $faker->boolean(95),
        ];
    }
}
