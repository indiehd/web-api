<?php

namespace Tests\Feature\Controllers;

use App\Contracts\AlbumRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\FeaturedRepositoryInterface;
use CountriesSeeder;

class FeaturedControllerTest extends ControllerTestCase
{
    /**
     * @var FeaturedRepositoryInterface $featured
     */
    protected $featured;

    /**
     * @var ArtistRepositoryInterface $artist
     */
    protected $artist;

    /**
     * @var AlbumRepositoryInterface $album
     */
    protected $album;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(CountriesSeeder::class);

        $this->featured = resolve(FeaturedRepositoryInterface::class);

        $this->artist = resolve(ArtistRepositoryInterface::class);

        $this->album = resolve(AlbumRepositoryInterface::class);
    }

    public function testArtistsReturnsOkStatusAndExpectedJsonStructure()
    {
        $artist = $this->createArtist();

        $this->makeAlbum([
            'artist_id' => $artist->id,
            'is_active' => true,
        ])->save();

        factory($this->featured->class())->create([
            'featurable_id' => $artist->id,
            'featurable_type' => $this->artist->class(),
        ]);

        $this->json('GET', route('featured.artists'))
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [$this->getJsonStructure()]
            ]);
    }

    protected function getJsonStructure()
    {
        return [
            'id',
            'label',
            'profile',
        ];
    }

    /**
     * Create an Artist.
     *
     * @param array $properties
     * @return \App\Artist
     */
    protected function createArtist(array $properties = [])
    {
        return factory($this->artist->class())->create($properties);
    }

    /**
     * Make an Album.
     *
     * @param array $properties
     * @return \App\Album
     */
    protected function makeAlbum(array $properties = [])
    {
        // This is the one property that can't be passed via the argument.

        $properties['artist_id'] = $properties['artist_id'] ?? $this->createArtist()->id;

        return factory($this->album->class())->make($properties);
    }
}
