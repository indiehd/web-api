<?php

namespace Database\Factories;

use App\Album;
use App\Artist;
use App\DigitalAsset;
use App\Genre;
use App\Song;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use IndieHD\Velkart\Models\Eloquent\Product;

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
                Song::factory()->create([
                    'album_id' => $album->id,
                    'track_number' => $i,
                    'is_active' => 1,
                ]);
            }

            // Create and associate a Digital Asset.

            $album = $album->fresh(['songs']);

            $album->asset()->save(DigitalAsset::factory()->make([
                'product_id' => Product::factory()->create([
                    'name' => $album->title,
                    'slug' => Str::slug($album->title),
                    'description' => $album->description,
                    'price' => $album->full_album_price ?? ($album->songs->count() * 100),
                ])->id,
                'asset_id' => $album->id,
                'asset_type' => App\Album::class,
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
            'artist_id' => Artist::factory(),
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
                'songs' => Song::factory()->count(rand(1, 20))->make(['is_active' => 1]),
            ];
        });
    }
}
