<?php

use App\DigitalAsset;
use App\Sku;
use App\Song;
use App\FlacFile;
use Faker\Generator as Faker;
use IndieHD\Velkart\Models\Eloquent\Product;

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

$factory->define(Song::class, function (Faker $faker) {

    return [
        'name' => $faker->company,
        'alt_name' => $faker->company,
        'flac_file_id' => function() {
            return factory(FlacFile::class)->create()->id;
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
});

$factory->afterCreating(Song::class, function ($song, $faker) {
    $song->asset()->save(factory(DigitalAsset::class)->make([
        'product_id' => factory(Product::class)->create([
            'name' => $song->name,
            'slug' => Str::slug($song->name),
            'description' => null,
            'price' => '10', // TODO Represent this with Money\Money via $song->price.
        ])->id,
        'asset_id' => $song->id,
        'asset_type' => App\Song::class,
    ]));
});
