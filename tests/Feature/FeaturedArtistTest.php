<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Carbon\Carbon;

use App\Contracts\FeaturedRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;

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
     * @var $profile ProfileRepositoryInterface
     */
    protected $profile;

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

        $this->profile = resolve(ProfileRepositoryInterface::class);

        // Two Artists that DO NOT meet every condition...

        // ... Artist doesn't have a Profile.

        $artist = $this->createArtistAndDeleteItsProfile();

        // ... Artist doesn't have at least one Album.

        $this->createArtist();

        // Three Artists that DO meet every condition.

        $artist = $this->createArtist();

        $this->makeAlbum(['artist_id' => $artist->id])->save();

        $artist = $this->createArtist();

        $this->makeAlbum(['artist_id' => $artist->id])->save();

        $artist = $this->createArtist();

        $this->makeAlbum(['artist_id' => $artist->id])->save();

        // Two more Artists...

        // ... Artist is currently featured (and has been for more than 7 days
        // but fewer than 180 days), which makes the Artist ineligible.

        $artist = $this->createArtist();

        $this->makeAlbum(['artist_id' => $artist->id])->save();

        $featured = $this->featured->create([
            'featurable_id' => $artist->id,
            'featurable_type' => $this->artist->class(),
        ]);

        $featured->created_at = Carbon::now()->subDays(7);
        $featured->save();

        // ... Artist was featured more than 180 days ago (implies longer than
        // 7 days ago, obviously), which makes the Artist eligible again.

        $artist = $this->createArtist();

        $this->makeAlbum(['artist_id' => $artist->id])->save();

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

    public function testFeaturableResultsHasExactlyThreeEntities()
    {
        $this->assertEquals(
            3,
            $this->artist->featurable()->get()->count()
        );
    }

    /*
    public function testFeaturedResultsAreExcluded()
    {

    }

    public function testFeaturedResultsAreEligible()
    {

    }

    public function testFeaturedEntityInstanceOfArtist()
    {

    }

    public function testFeaturedArtistExists()
    {

    }
    */

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

    /**
     * Make an Order Item.
     *
     * @param array $properties
     * @return \App\OrderItem
     */
    protected function makeOrderItem($properties = [])
    {
        return factory($this->orderItem->class())->make([
            'order_id' => $properties['order_id'] ?? $this->order->create(
                    factory($this->order->class())->raw()
                )->id,
            'orderable_id' => $properties['orderable_id'] ?? $this->repo->create(
                    $this->makeAlbum()->toArray()
                )->id,
            'orderable_type' => $properties['orderable_type'] ?? $this->repo->class(),
        ]);
    }
}
