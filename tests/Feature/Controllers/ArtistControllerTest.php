<?php

namespace Tests\Feature\Controllers;

use App\Contracts\ArtistRepositoryInterface;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use DatabaseSeeder;

use App\Artist;
use App\Contracts\ProfileRepositoryInterface;

class ArtistControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var  $profile  ProfileRepositoryInterface
     */
    protected $profile;

    public function setUp()
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);

        $this->profile = resolve(ProfileRepositoryInterface::class);

        $this->artist = resolve(ArtistRepositoryInterface::class);
    }

    public function test_index_returnsMultipleJsonObjects()
    {
        $this->json('GET', route('artist.index'))
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'moniker',
                        'alt_moniker',
                        'city',
                        'territory',
                        'country_code',
                        'official_url',
                        'profile_url',
                        'rank',
                    ]
                ]
            ]);
    }

    public function test_store()
    {
        $profile = factory($this->profile->class())->make()->toArray();

        $this->artist->create($profile);

        $this->json('POST', route('artist.store'), $profile)
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'moniker',
                    'alt_moniker',
                    'city',
                    'territory',
                    'country_code',
                    'official_url',
                    'profile_url',
                    'rank',
                ]
            ]);
    }

    public function test_show_returnsOneJsonObject()
    {
        $this->json('GET', route('artist.show', ['id' => Artist::inRandomOrder()->first()->id]))
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'moniker',
                    'alt_moniker',
                    'city',
                    'territory',
                    'country_code',
                    'official_url',
                    'profile_url',
                    'rank',
                ]
            ]);
    }
}
