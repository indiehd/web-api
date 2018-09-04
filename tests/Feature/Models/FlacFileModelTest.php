<?php

namespace Tests\Feature\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use DatabaseSeeder;
use App\FlacFile;
use App\Song;

class FlacFileModelTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Ensure that any random FlacFile has one or more Songs.
     *
     * @return void
     */
    public function test_song_randomFlacFile_hasManySongs()
    {
        $this->assertInstanceOf(Song::class, FlacFile::inRandomOrder()->first()->songs->first());
    }
}
