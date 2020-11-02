<?php

namespace Tests\Feature\Featured;

use App\Contracts\AlbumRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\DigitalAssetRepositoryInterface;
use App\Contracts\FeaturedRepositoryInterface;
use App\Contracts\SongRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeaturedArtistEligibilityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var FeaturedRepositoryInterface
     */
    protected $featured;

    /**
     * @var ArtistRepositoryInterface
     */
    protected $album;

    /**
     * @var SongRepositoryInterface
     */
    protected $song;

    /**
     * @var ArtistRepositoryInterface
     */
    protected $artist;

    /**
     * @var array
     */
    protected $eligibleArtists = [];

    /**
     * @var DigitalAssetRepositoryInterface
     */
    protected $digitalAsset;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seed('CountriesSeeder');

        $this->featured = resolve(FeaturedRepositoryInterface::class);

        $this->album = resolve(AlbumRepositoryInterface::class);

        $this->song = resolve(SongRepositoryInterface::class);

        $this->artist = resolve(ArtistRepositoryInterface::class);

        $this->digitalAsset = resolve(DigitalAssetRepositoryInterface::class);

        // Artists that DO NOT meet every condition...

        // ... Artist doesn't have a Profile.

        $artist = $this->createArtistAndDeleteItsProfile();

        // ... Artist doesn't have at least one Album.

        $this->createArtist();

        // ... Artist has at least one Album, but the Album is inactive.

        $this->createAlbum(['is_active' => false]);

        // ... Artist has one Album, but it doesn't contain at least one Song.

        $album = $this->createAlbum();

        $album->songs()->each(function ($song) {
            $song->delete();
        });

        // ... Artist has one Album, and it contains at least one Song, but the Song is inactive.

        $album = $this->createAlbum();

        $album->songs()->each(function ($song) {
            $song->delete();
        });

        $song = Factory::factoryForModel($this->song->class())->make([
            'track_number' => 1,
            'is_active' => false,
            'album_id' => $album->id,
        ]);

        Factory::factoryForModel($this->song->class())->create($song->toArray());

        // One Artist that DOES meet every condition.

        $artist = $this->createArtist();

        $this->eligibleArtists[] = $artist;

        $this->createAlbum([
            'artist_id' => $artist->id,
            'is_active' => true,
        ]);

        // Two more Artists...

        // ... Artist is currently featured (and has been for more than 7 days
        // but fewer than 180 days), which makes the Artist ineligible.

        $artist = $this->createArtist();

        $this->makeAlbum([
            'artist_id' => $artist->id,
            'is_active' => true,
        ])->save();

        $featured = $this->featured->create([
            'featurable_id' => $artist->id,
            'featurable_type' => $this->artist->class(),
        ]);

        $featured->created_at = Carbon::now()->subDays(7);
        $featured->save();

        // ... Artist was featured more than 180 days ago, which makes the
        // Artist eligible again.

        $artist = $this->createArtist();

        $this->eligibleArtists[] = $artist;

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
    }

    private function createArtistAndDeleteItsProfile()
    {
        $artist = $this->createArtist();

        $artist->profile()->delete();

        return $artist;
    }

    public function testFeaturableResultsMatchExpected()
    {
        $this->assertEquals(
            collect($this->eligibleArtists)->pluck('id'),
            $this->artist->featurable()->get()->pluck('id')
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
        return Factory::factoryForModel($this->artist->class())->create($properties);
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

        return Factory::factoryForModel($this->album->class())->create($properties);
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

        return Factory::factoryForModel($this->album->class())->make($properties);
    }

    /**
     * Make a Digital Asset.
     *
     * @param array $properties
     * @return \App\DigitalAsset
     */
    protected function makeDigitalAsset($properties = [])
    {
        return Factory::factoryForModel($this->digitalAsset->class())->make([
            'asset_id' => $properties['asset_id'] ?? $this->repo->create(
                $this->makeAlbum()->toArray()
            )->id,
            'asset_type' => $properties['aaset_type'] ?? $this->repo->class(),
        ]);
    }
}
