<?php

namespace Tests\Feature\Controllers;

use CountriesSeeder;

use App\Contracts\CountryRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Http\Requests\StoreArtist;
use App\Http\Requests\UpdateArtist;

class ArtistControllerTest extends ControllerTestCase
{
    /**
     * @var StoreArtist
     */
    protected $storeArtist;

    /**
     * @var UpdateArtist
     */
    protected $updateArtist;

    public function setUp()
    {
        parent::setUp();

        $this->seed(CountriesSeeder::class);

        $this->country = resolve(CountryRepositoryInterface::class);
        $this->artist = resolve(ArtistRepositoryInterface::class);
        $this->profile = resolve(ProfileRepositoryInterface::class);

        // New-up the Form Request classes directly, only so we can get the
        // validation rules from them dynamically.

        $this->storeArtist = new StoreArtist();

        $this->updateArtist = new UpdateArtist();
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
            'label',
            'profile',
            'songs',
            'albums',
        ];
    }

    public function getAllInputsInValidState()
    {
        return [
            'moniker' => 'Joey\'s Basement Band',
            'alt_moniker' => 'No Longer in the Garage',
            'email' => 'foo@bar.com',
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
            'email' => 'foo@',
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

        $this->json('GET', route('artists.index'))
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    $this->getJsonStructure()
                ]
            ]);
    }

    public function test_store_withValidInputs_returnsOneJsonObject()
    {
        $this->json('POST', route('artists.store'), $this->getAllInputsInValidState())
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => $this->getJsonStructure()
            ]);
    }

    public function test_store_withInvalidInputs_returnsErrorMessage()
    {
        $this->json('POST', route('artists.store'), $this->getInputsInInvalidState())
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => array_keys($this->storeArtist->rules())
            ]);
    }

    public function test_show_returnsOneJsonObject()
    {
        $artist = $this->spawnArtist();

        $this->json('GET', route('artists.show', ['id' => $artist->id]))
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

        $this->json('PUT', route('artists.update', ['id' => $artist->id]), $inputs)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $artist->id,
                    'label' => null,
                    'profile' => $inputs,
                    'songs' => [],
                    'albums' => [],
                ]
            ]);
    }

    public function test_update_withInvalidInputs_returnsErrorMessage()
    {
        $artist = $this->spawnArtist();

        $this->json('PUT', route('artists.update', ['id' => $artist->id]), $this->getInputsInInvalidState())
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => array_keys($this->updateArtist->rules())
            ]);
    }
}
