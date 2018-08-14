<?php

namespace Tests\Feature\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use DatabaseSeeder;
use App\Album;

class GenreModelTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Ensure that any random Album has one or more Genres.
     *
     * @return void
     */
    public function test_albums_randomAlbum_hasManyGenres()
    {
        $this->assertFalse(Album::inRandomOrder()->first()->genres->isEmpty());
    }
}
