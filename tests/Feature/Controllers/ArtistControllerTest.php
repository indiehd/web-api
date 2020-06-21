<?php

namespace Tests\Feature\Controllers;

use CountriesSeeder;

use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\UserRepositoryInterface;

class ArtistControllerTest extends ControllerTestCase
{

    /**
     * @var ArtistRepositoryInterface $artist
     */
    protected $artist;

    /**
     * @var ProfileRepositoryInterface $profile
     */
    protected $profile;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(CountriesSeeder::class);

        $this->artist = resolve(ArtistRepositoryInterface::class);
        $this->profile = resolve(ProfileRepositoryInterface::class);
        $this->user = resolve(UserRepositoryInterface::class);
    }

    protected function createArtist()
    {
        return factory($this->artist->class())->create();
    }

    protected function getJsonStructure()
    {
        return [
            'id',
            'label',
            'profile',
            'songs',
            'albums',
        ];
    }

    protected function getAllInputsInValidState()
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

    public function test_all_returnsOkStatusAndExpectedJsonStructure()
    {
        $this->createArtist();

        $this->json('GET', route('artists.index'))
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    $this->getJsonStructure()
                ]
            ]);
    }

    public function test_show_returnsOkStatusAndExpectedJsonStructure()
    {
        $artist = $this->createArtist();

        $this->json('GET', route('artists.show', ['id' => $artist->id]))
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->getJsonStructure()
            ]);
    }

    public function test_store_withValidInput_returnsOkStatusAndExpectedJsonStructure()
    {
        $user = factory($this->user->class())->create();

        $this
            ->actingAs($user)
            ->json('POST', route('artists.store'), $this->getAllInputsInValidState())
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => $this->getJsonStructure()
            ]);
    }

    public function test_store_withInvalidInput_returnsUnprocessableEntityStatusAndExpectedJsonStructure()
    {
        $this->json('POST', route('artists.store'), [])
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors'
            ]);
    }

    public function test_update_withValidInput_returnsOkStatusAndExpectedJsonStructure()
    {
        $artist = $this->createArtist();

        $inputs = $this->getAllInputsInValidState();

        $inputs['alt_moniker'] = 'Back in the Garage';

        $this
            ->actingAs($artist->user)
            ->json('PUT', route('artists.update', ['id' => $artist->id]), $inputs)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->getJsonStructure()
            ]);
    }

    public function test_update_withInvalidInput_returnsUnprocessableEntityStatusAndExpectedJsonStructure()
    {
        $artist = $this->createArtist();

        $this->json('PUT', route('artists.update', ['id' => $artist->id]), ['email' => 'foo@'])
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors'
            ]);
    }

    public function test_destroy_withValidInput_returnsOkStatusAndExpectedJsonStructure()
    {
        $artist = $this->createArtist();

        $this->actingAs($artist->user)
            ->json('DELETE', route('artists.destroy', ['id' => $artist->id]))
            ->assertStatus(200)
            ->assertJsonStructure([]);
    }

    public function test_destroy_withInvalidInput_returnsUnprocessableEntityStatus()
    {
        $artist = $this->createArtist();

        $this->actingAs($artist->user)
            ->json('DELETE', route('artists.destroy', ['id' => 'foo']))
            ->assertStatus(404);
    }

    public function test_destroy_withMissingInput_returnsMethodNotAllowedStatus()
    {
        $this->json('DELETE', str_replace('foo', '', route('artists.destroy', ['id' => 'foo'])))
            ->assertStatus(405);
    }
}
