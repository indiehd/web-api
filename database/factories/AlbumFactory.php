<?php

use App\Album;
use App\Genre;
use Faker\Generator as Faker;

use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;
use App\Contracts\SongRepositoryInterface;

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
        'is_active' => $faker->boolean(80)
    ];
});

$factory->state($album->class(), 'withSongs', function ($faker) use ($song) {
    return [
        'songs' => factory($song->class(), rand(1, 20))->make(),
    ];
});

$factory->afterCreating($album->class(), function ($album, $faker) use ($song) {

    // Create and associate some Songs.

    for ($i = 1; $i < rand(2, 21); $i++) {
        $s = factory($song->class())->create([
            'album_id' => $album->id,
            'track_number' => $i
        ]);

        $s->album()->associate($album)->save();
    }

    // Fetch and attach some Genres.

    $genres = Genre::inRandomOrder()->take(rand(1, 10))->get();

    $album->genres()->attach($genres->pluck('id'));
});
