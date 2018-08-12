<?php

use App\Album;
use App\Artist;
use App\Genre;
use Faker\Generator as Faker;

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

$factory->define(Album::class, function (Faker $faker) {

    return [
        'artist_id' => function() {
            return Artist::inRandomOrder()->first()->id;
        },
        'title' => $faker->company,
        'alt_title' => $faker->company,
        'year' => $faker->year('now'),
        'description' => $faker->sentence(10),
        'has_explicit_lyrics' => $faker->boolean(25),
        'is_active' => $faker->boolean(80)
    ];
});

$factory->afterCreating(Album::class, function ($album, $faker) {
    $genres = Genre::inRandomOrder()->take(rand(0, 10))->get();

    $album->genres()->attach($genres->pluck('id'));
});
