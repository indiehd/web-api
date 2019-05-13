<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
    }

    public function testWhenArtistIsFeaturedItHasAccountProfile()
    {
        $album = $this->makeAlbum();

        factory($this->featured->class())->create([
            'featurable_id' => $album->artist->id,
            'featurable_type' => $this->artist->class(),
        ]);

        $featured = $this->featured->artists()->first();

        $this->assertInstanceOf(
            $this->profile->class(),
            $featured->featurable->profile
        );
    }

    /**
     * Create an Album.
     *
     * @param array $properties
     * @return \App\Album
     */
    protected function makeAlbum(array $properties = [])
    {
        $artist = factory($this->artist->class())->create();

        // This is the one property that can't be passed via the argument.

        $properties['artist_id'] = $artist->id;

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
