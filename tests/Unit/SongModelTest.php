<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Song;
use App\Album;

class SongModelTest extends TestCase
{
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
