<?php

namespace Tests\Feature\Featured;

use Carbon\Carbon;

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
     * @var $expectedValues array
     */
    protected $expectedValues = [];

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

        // Create an Artist and attendant Feature that is 180 days old.

        $artist = $this->createArtist();

        $this->createAlbum([
            'artist_id' => $artist->id,
            'is_active' => true,
        ]);

        $featured = $this->featured->create([
            'featurable_id' => $artist->id,
            'featurable_type' => $this->artist->class(),
        ]);

        $featured->created_at = Carbon::now()->subDays(180);
        $featured->save();

        $this->expectedValues['olderFeatured'] = $featured;

        // Create an Artist that has never been featured.

        $artist = $this->createArtist();

        $this->createAlbum([
            'artist_id' => $artist->id,
            'is_active' => true,
        ]);
    }

    public function testWhenCalledFeatureArtisanCommandFeaturesExpectedEntities()
    {
        Artisan::call('feature:process');

        // Ensure that exactly two Featured entities are returned: one for the
        // first Artist whose first Featured we expired manually, because the
        // Artisan call will create a new one for it; and one for the second
        // Artist.

        $this->assertEquals(
            2,
            $this->featured->artists()->get()->count()
        );

        // Ensure that the Feature retrieved for the first Artist we created
        // is not the older one, which means (per the previous assertion) that
        // it must be the newer Feature that the Artisan command created .

        $this->assertNotEquals(
            $this->expectedValues['olderFeatured']->id,
            $this->featured->artists()->first()->id
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
     * Create an Album.
     *
     * @param array $properties
     * @return \App\Album
     */
    protected function createAlbum(array $properties = [])
    {
        // This is the one property that can't be passed via the argument.

        $properties['artist_id'] = $properties['artist_id'] ?? $this->createArtist()->id;

        return factory($this->album->class())->create($properties);
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

    /**
     * Make an Album.
     *
     * @param array $properties
     * @return \App\Album
     */
    protected function makeAlbumWithSongs(array $properties = [])
    {
        // This is the one property that can't be passed via the argument.

        $properties['artist_id'] = $properties['artist_id'] ?? $this->createArtist()->id;

        // Use the withSongs factory state.

        $album = factory($this->album->class())
            ->state('withSongs')
            ->make($properties);

        // Cast the songs to an array, too.

        $album['songs'] = $album['songs']->toArray();

        return $album;
    }
}
