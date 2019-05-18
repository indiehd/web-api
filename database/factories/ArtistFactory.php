<?php

use Faker\Generator as Faker;

use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\LabelRepositoryInterface;
use App\Contracts\CatalogEntityRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;

$artist = resolve(ArtistRepositoryInterface::class);
$label = resolve(LabelRepositoryInterface::class);
$catalogEntity = resolve(CatalogEntityRepositoryInterface::class);
$user = resolve(UserRepositoryInterface::class);
$profile = resolve(ProfileRepositoryInterface::class);

$factory->define($artist->class(), function (Faker $faker) {
    return [];
});

$factory->state($artist->class(), 'onLabel', [
    'label_id' => function() use ($label) {
        return factory($label->class())->create()->id;
    },
]);

$factory->afterCreating($artist->class(), function ($a, $faker) use ($artist, $catalogEntity, $user, $profile) {
    factory($catalogEntity->class())->create([
        'user_id' => factory($user->class())->create()->id,
        'catalogable_id' => $a->id,
        'catalogable_type' => $artist->class()
    ]);

    factory($profile->class())->create([
        'profilable_id' => $a->id,
        'profilable_type' => $artist->class()
    ]);
});
