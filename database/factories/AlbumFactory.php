<?php

namespace Database\Factories;

use App\Album;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\DigitalAssetRepositoryInterface;
use App\Contracts\SongRepositoryInterface;
use App\Genre;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use IndieHD\Velkart\Contracts\Repositories\Eloquent\ProductRepositoryContract;

class AlbumFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Album::class;

    public function configure()
    {
        return $this->afterCreating(function (Album $album) {

            // Create and associate some Songs.

            $songs = rand(2, 21);

            for ($i = 1; $i < $songs; $i++) {
                static::factoryForModel(resolve(SongRepositoryInterface::class)->class())->create([
                    'album_id' => $album->id,
                    'track_number' => $i,
                    'is_active' => 1,
                ]);
            }

            // Create and associate a Digital Asset.

            $album = $album->fresh(['songs']);

            $album->asset()->save(static::factoryForModel(resolve(DigitalAssetRepositoryInterface::class)->class())->make([
                'product_id' => resolve(ProductRepositoryContract::class)->create([
                    'name' => $album->title,
                    'slug' => Str::slug($album->title),
                    'description' => $album->description,
                    'price' => $album->full_album_price ?? ($album->songs->count() * 100),
                    'sku' => $this->faker->unique()->numberBetween(1111111, 999999),
                    'cover' => UploadedFile::fake()->image('product.png', 600, 600),
                    'quantity' => 10,
                    'status' => 1,
                ])->id,
                'asset_id' => $album->id,
                'asset_type' => Album::class,
            ]));

            // Fetch and attach some Genres.

            $genres = Genre::inRandomOrder()->take(rand(1, 10))->get();

            $album->genres()->attach($genres->pluck('id'));
        });
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
            'artist_id' => static::factoryForModel(resolve(ArtistRepositoryInterface::class)->class()),
            'title' => $faker->company,
            'alt_title' => $faker->company,
            'year' => $faker->year('now'),
            'description' => $faker->sentence(10),
            'has_explicit_lyrics' => $faker->boolean(25),
            'is_active' => $faker->boolean(80),
        ];
    }

    public function withSongs()
    {
        return $this->state(function () {
            return [
                'songs' => static::factoryForModel(resolve(SongRepositoryInterface::class)->class())->count(rand(1, 20))->make(['is_active' => 1]),
            ];
        });
    }
}
