<?php

namespace Database\Factories;

use App\Artist;
use App\Contracts\CatalogEntityRepositoryInterface;
use App\Contracts\LabelRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArtistFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Artist::class;

    public function configure()
    {
        return $this->afterCreating(function (Artist $artist) {
            static::factoryForModel(resolve(CatalogEntityRepositoryInterface::class)->class())->create([
                'user_id' => static::factoryForModel(resolve(UserRepositoryInterface::class)->class())->create()->id,
                'catalogable_id' => $artist->id,
                'catalogable_type' => Artist::class
            ]);

            static::factoryForModel(resolve(ProfileRepositoryInterface::class)->class())->create([
                'profilable_id' => $artist->id,
                'profilable_type' => Artist::class
            ]);
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
        ];
    }

    public function onLabel()
    {
        return $this->state(function () {
            return [
                'label_id' => static::factoryForModel(resolve(LabelRepositoryInterface::class)->class())->create()->id
            ];
        });
    }
}
