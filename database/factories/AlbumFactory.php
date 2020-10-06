<?php

use App\Contracts\AlbumRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\SongRepositoryInterface;
use App\DigitalAsset;
use App\Genre;
use Faker\Generator as Faker;
use IndieHD\Velkart\Models\Eloquent\Product;

$artist = resolve(ArtistRepositoryInterface::class);
$album = resolve(AlbumRepositoryInterface::class);
$song = resolve(SongRepositoryInterface::class);

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define($album->class(), function (Faker $faker) use ($artist, $song) {

    return [
        'artist_id' => function() use ($artist) {
            return factory($artist->class())->create()->id;
        },
        'title' => $faker->company,
        'alt_title' => $faker->company,
        'year' => $faker->year('now'),
        'description' => $faker->sentence(10),
        'has_explicit_lyrics' => $faker->boolean(25),
        'is_active' => $faker->boolean(80),
    ];
});

$factory->state($album->class(), 'withSongs', function ($faker) use ($song) {
    return [
        'songs' => factory($song->class(), rand(1, 20))->make(['is_active' => 1]),
    ];
});

$factory->afterCreating($album->class(), function ($album, $faker) use ($song) {

    // Create and associate some Songs.

    for ($i = 1; $i < rand(2, 21); $i++) {
        $s = factory($song->class())->create([
            'album_id' => $album->id,
            'track_number' => $i,
            'is_active' => 1,
        ]);

        $s->album()->associate($album)->save();
    }

    // Create and associate a Digital Asset.

    $album->asset()->save(factory(DigitalAsset::class)->make([
        'product_id' => factory(Product::class)->create([
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
