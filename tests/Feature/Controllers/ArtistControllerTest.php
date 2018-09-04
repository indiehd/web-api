<?php

namespace Tests\Feature\Controllers;

use CountriesSeeder;

use App\Contracts\CountryRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;

class ArtistControllerTest extends ControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->seed(CountriesSeeder::class);

        $this->country = resolve(CountryRepositoryInterface::class);
        $this->artist = resolve(ArtistRepositoryInterface::class);
        $this->profile = resolve(ProfileRepositoryInterface::class);
    }

    public function spawnArtist()
    {
        $artist = $this->artist->model()->create();

        $this->profile->model()->create(
            $this->getAllInputsInValidState() + [
                'profilable_id' => $artist->id,
                'profilable_type' => $this->artist->class()
            ]
        );

        return $artist;
    }

    public function getJsonStructure()
    {
        return [
            'id',
            'moniker',
            'alt_moniker',
            'city',
            'territory',
            'country_code',
            'official_url',
            'profile_url',
            'rank',
        ];
    }

    public function getAllInputsInValidState()
    {
        return [
            'moniker' => 'Joey\'s Basement Band',
            'alt_moniker' => 'No Longer in the Garage',
            'city' => 'New York City',
            'territory' => 'New York',
            'country_code' => 'US',
            'official_url' => 'https://joeysbasementband.com',
            'profile_url' => 'joeysbasementband',
        ];
    }

    public function getInputsInInvalidState()
    {
        return [
            'alt_moniker' => str_random(256),
            'city' => str_random(256),
            'territory' => str_random(256),
            'country_code' => 'United States',
            'official_url' => 'joeysbasementband.com',
            'profile_url' => str_random(65),
        ];
    }

    public function test_index_returnsMultipleJsonObjects()
    {
        $this->spawnArtist();

        $this->json('GET', route('artist.index'))
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    $this->getJsonStructure()
                ]
            ]);
    }

    public function test_store_withValidInputs_returnsOneJsonObject()
    {
        $this->json('POST', route('artist.store'), $this->getAllInputsInValidState())
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => $this->getJsonStructure()
            ]);
    }

    public function test_store_withInvalidInputs_returnsErrorMessage()
    {
        $this->json('POST', route('artist.store'), $this->getInputsInInvalidState())
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => array_keys($this->getAllInputsInValidState())
            ]);
    }

    public function test_show_returnsOneJsonObject()
    {
        $artist = $this->spawnArtist();

        $this->json('GET', route('artist.show', ['id' => $artist->id]))
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->getJsonStructure()
            ]);
    }

    public function test_update_withValidInputs_returnsJsonObjectMatchingInputs()
    {
        $artist = $this->spawnArtist();

        $inputs = $this->getAllInputsInValidState();

        $inputs['alt_moniker'] = 'Back in the Garage';

        $this->json('PUT', route('artist.update', ['id' => $artist->id]), $inputs)
            ->assertStatus(200)
            ->assertJson([
                'data' => $inputs
            ]);
    }

    public function test_update_withInvalidInputs_returnsErrorMessage()
    {
        $artist = $this->spawnArtist();

        $this->json('PUT', route('artist.update', ['id' => $artist->id]), $this->getInputsInInvalidState())
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => array_keys($this->getAllInputsInValidState())
            ]);
    }
}
