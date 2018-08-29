<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use DatabaseSeeder;

use App\Artist;

class ArtistControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
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
