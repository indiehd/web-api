<?php

namespace Tests\Feature\Featured;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Artisan;

use App\Contracts\FeaturedRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;

class FeaturedArtistTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var $featured FeaturedRepositoryInterface
     */
    protected $featured;

    /**
     * @var $artist ArtistRepositoryInterface
     */
    protected $album;

    /**
     * @var $artist ArtistRepositoryInterface
     */
    protected $artist;

    /**
     * @var $eligibleArtists array
     */
    #protected $eligibleArtists = [];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seed('CountriesSeeder');

        $this->featured = resolve(FeaturedRepositoryInterface::class);

        $this->album = resolve(AlbumRepositoryInterface::class);

        $this->artist = resolve(ArtistRepositoryInterface::class);

        $artist = $this->createArtist();

        $this->makeAlbum([
            'artist_id' => $artist->id,
            'is_active' => true,
        ])->save();
    }

    public function testWhenCalledFeatureArtisanCommandFeaturesExpectedEntities()
    {
        Artisan::call('feature:process');

        $this->assertEquals(
            1,
            $this->featured->artists()->count()
        );
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
