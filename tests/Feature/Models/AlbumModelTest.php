<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use AlbumsSeeder;
use GenresSeeder;
use App\Album;

class AlbumModelTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->seed(GenresSeeder::class);
        $this->seed(AlbumsSeeder::class);
    }

    /**
     * Ensure that any random Album has one or more Songs.
     *
     * @return true
     */
    public function test_songs_randomAlbum_returnsNonEmptyCollection()
    {
        $this->assertFalse(Album::inRandomOrder()->first()->songs->isEmpty());
    }

    /**
     * Ensure that any random Album has one or more Genres.
     *
     * @return true
     */
    public function test_genres_randomAlbum_returnsNonEmptyCollection()
    {
        $this->assertFalse(Album::inRandomOrder()->first()->genres->isEmpty());
    }
}
