<?php

namespace Tests\Feature\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use DatabaseSeeder;
use App\Album;
use App\Genre;

class GenreModelTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Ensure that any random Album belongs to one or more Genres.
     *
     * @return void
     */
    public function test_albums_randomAlbum_belongsToManyGenres()
    {
        $this->assertInstanceOf(Genre::class, Album::inRandomOrder()->first()->genres->first());
    }
}
