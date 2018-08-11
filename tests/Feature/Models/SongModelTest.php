<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use AlbumsSeeder;
use App\Song;
use App\Album;

class SongModelTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->seed(AlbumsSeeder::class);
    }

    /**
     * Ensure that any random Song belongs to an Album.
     *
     * @return true
     */
    public function test_albums_randomSong_belongsToAlbum()
    {
        $this->assertInstanceOf(Album::class, Song::inRandomOrder()->first()->album);
    }
}
