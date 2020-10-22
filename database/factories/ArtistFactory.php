<?php

namespace Database\Factories;

use App\Artist;
use App\CatalogEntity;
use App\Label;
use App\Profile;
use App\User;
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

            CatalogEntity::factory()->creatte([
                'user_id' => User::factory()->create()->id,
                'catalogable_id' => $artist->id,
                'catalogable_type' => Artist::class
            ]);

            Profile::factory()->create([
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
                'label_id' => Label::factory()->create()->id
            ];
        });
    }
}
