<?php

use Faker\Generator as Faker;

use App\Contracts\ArtistRepositoryInterface;

$repo = resolve(ArtistRepositoryInterface::class);

$factory->define($repo->class(), function (Faker $faker) {
    return [];
});

$factory->state(App\Artist::class, 'onLabel', [
    'label_id' => function() {
        return factory(App\Label::class)->create()->id;
    },
]);

$factory->afterCreating($repo->class(), function ($artist, $faker) use ($repo) {
    factory(App\CatalogEntity::class)->create([
        'user_id' => factory(App\User::class)->create()->id,
        'catalogable_id' => $artist->id,
        'catalogable_type' => $repo->class()
    ]);

    factory(App\Profile::class)->create([
        'profilable_id' => $artist->id,
        'profilable_type' => $repo->class()
    ]);
});
