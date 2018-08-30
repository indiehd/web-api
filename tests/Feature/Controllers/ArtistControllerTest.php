<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Country;
use App\Artist;
use App\Profile;

class ArtistControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $country = new Country();
        $country->code = 'US';
        $country->name = 'United States';
        $country->save();

        Artist::create();

        Profile::create(
            $this->getAllInputsInValidState() + [
                'profilable_id' => 1,
                'profilable_type' => Artist::class
            ]
        );
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

    public function test_index_returnsMultipleJsonObjects()
    {
        $this->json('GET', route('artist.index'))
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    $this->getJsonStructure()
                ]
            ]);
    }

    public function test_store_returnsOneJsonObject()
    {
        $this->json('POST', route('artist.store'), $this->getAllInputsInValidState())
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => $this->getJsonStructure()
            ]);
    }

    public function test_show_returnsOneJsonObject()
    {
        $this->json('GET', route('artist.show', ['id' => 1]))
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->getJsonStructure()
            ]);
    }
}
