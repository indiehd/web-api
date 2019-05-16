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

        $this->makeAlbum([
            'artist_id' => $artist->id,
            'is_active' => true,
        ])->save();

        $featured = $this->featured->create([
            'featurable_id' => $artist->id,
            'featurable_type' => $this->artist->class(),
        ]);

        $featured->created_at = Carbon::now()->subDays(180);
        $featured->save();

        // Create an Artist that has never been featured.

        $artist = $this->createArtist();

        $this->makeAlbum([
            'artist_id' => $artist->id,
            'is_active' => true,
        ])->save();
    }

    public function testWhenCalledFeatureArtisanCommandFeaturesExpectedEntities()
    {
        Artisan::call('feature:process');

        // Ensure that only one Featured record is returned, and that it's the
        // more recent of the two.

        $this->assertEquals(
            1,
            $this->featured->artists()->count()
        );

        $this->assertEquals(
            2,
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
