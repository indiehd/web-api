<?php

namespace Tests\Feature\Models;

use Symfony\Component\VarDumper\Cloner\Data;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use DatabaseSeeder;
use App\Album;
use App\Artist;

class AlbumModelTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Ensure that any random Album has one Artist.
     *
     * @return void
     */
    public function test_artist_randomAlbum_hasOneArtist()
    {
        $this->assertInstanceOf(Artist::class, Album::inRandomOrder()->first()->artist);
    }

    /**
     * Ensure that any random Album has one or more Songs.
     *
     * @return void
     */
    public function test_songs_randomAlbum_returnsNonEmptyCollection()
    {
        $this->assertFalse(Album::inRandomOrder()->first()->songs->isEmpty());
    }

    /**
     * Ensure that any random Album has one or more Genres.
     *
     * @return void
     */
    public function test_genres_randomAlbum_returnsNonEmptyCollection()
    {
        $this->assertFalse(Album::inRandomOrder()->first()->genres->isEmpty());
    }
}
