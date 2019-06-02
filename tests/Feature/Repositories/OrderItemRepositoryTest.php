<?php

namespace Tests\Feature\Repositories;

use CountriesSeeder;
use App\Contracts\OrderRepositoryInterface;
use App\Contracts\OrderItemRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\SongRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderItemRepositoryTest extends RepositoryCrudTestCase
{
    /**
     * @var ProfileRepositoryInterface $profile
     */
    protected $profile;

    /**
     * @var ArtistRepositoryInterface $artist
     */
    protected $artist;

    /**
     * @var AlbumRepositoryInterface $album
     */
    protected $album;

    /**
     * @var SongRepositoryInterface $song
     */
    protected $song;

    /**
     * @var OrderRepositoryInterface $order
     */
    protected $order;

    /**
     * @inheritdoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seed(CountriesSeeder::class);

        $this->profile = resolve(ProfileRepositoryInterface::class);

        $this->artist = resolve(ArtistRepositoryInterface::class);

        $this->album = resolve(AlbumRepositoryInterface::class);

        $this->song = resolve(SongRepositoryInterface::class);

        $this->order = resolve(OrderRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function setRepository()
    {
        $this->repo = resolve(OrderItemRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function testCreateStoresNewResource()
    {
        $item = $this->makeOrderItem();

        $this->assertInstanceOf(
            $this->repo->class(),
            $this->repo->create($item->toArray())
        );
    }

    /**
     * @inheritdoc
     */
    public function testUpdateUpdatesResource()
    {
        $item = $this->repo->create($this->makeOrderItem()->toArray());

        $album = factory($this->album->class())->create($this->makeAlbum()->toArray());

        $newValue = $album->id;

        $property = 'orderable_id';

        $this->repo->update($item->id, [
            $property => $newValue,
        ]);

        $this->assertTrue(
            $this->repo->findById($item->id)->{$property} === $newValue
        );
    }

    /**
     * @inheritdoc
     */
    public function testUpdateReturnsModelInstance()
    {
        $item = $this->repo->create($this->makeOrderItem()->toArray());

        $updated = $this->repo->update($item->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function testDeleteDeletesResource()
    {
        $item = $this->repo->create($this->makeOrderItem()->toArray());

        $item->delete();

        try {
            $this->repo->findById($item->id);
        } catch (ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Ensure that deleting the last Order Item in an Order causes the Order
     * to be deleted, too.
     */
    public function testDeleteLastItemDeletesOrder()
    {
        $item = $this->repo->create($this->makeOrderItem()->toArray());

        $item->delete();

        try {
            $this->assertNull($this->order->findById($item->order_id));
        } catch (ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Ensure that when an Order is related to an Order Item, the Order
     * Item belongs to an Order.
     *
     * @return void
     */
    public function testWhenOrderItemRelatedToOrderItBelongsToOrder()
    {
        $item = $this->repo->create($this->makeOrderItem()->toArray());

        $this->assertInstanceOf($this->order->class(), $item->order);
    }

    /**
     * Ensure that a sold Album morphs to Orderable.
     *
     * @return void
     */
    public function testWhenAlbumSoldItMorphsToOrderable()
    {
        $soldAlbum = $this->repo->create($this->makeOrderItem()->toArray());

        $this->assertInstanceOf($this->album->class(), $soldAlbum->orderable);
    }

    /**
     * Ensure that a sold Song morphs to Orderable.
     *
     * @return void
     */
    public function testWhenSongSoldItMorphsToOrderable()
    {
        $song = $this->createSong();

        $soldSong = $this->repo->create($this->makeOrderItem([
            'orderable_id' => $song->id,
            'orderable_type' => $this->song->class(),
        ])->toArray());

        $this->assertInstanceOf($this->song->class(), $soldSong->orderable);
    }

    /**
     * Create an Album.
     *
     * @param array $properties
     * @return \App\Album
     */
    protected function createAlbum(array $properties = [])
    {
        $artist = $this->artist->create(
            factory($this->artist->class())->raw(
                factory($this->profile->class())->raw()
            )
        );

        // This is the one property that can't be passed via the argument.

        $properties['artist_id'] = $artist->id;

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
        $artist = $this->artist->create(
            factory($this->artist->class())->raw(
                factory($this->profile->class())->raw()
            )
        );

        // This is the one property that can't be passed via the argument.

        $properties['artist_id'] = $artist->id;

        return factory($this->album->class())->make($properties);
    }

    /**
     * Create a Song.
     *
     * @return \App\Song
     */
    protected function createSong()
    {
        $album = factory($this->album->class())->create();

        return $album->songs()->first();
    }

    /**
     * Make an Order Item.
     *
     * @param array $properties
     * @return \App\OrderItem
     */
    protected function makeOrderItem($properties = [])
    {
        return factory($this->repo->class())->make([
            'order_id' => $properties['order_id'] ?? $this->order->create(
                factory($this->order->class())->raw()
            )->id,
            'orderable_id' => $properties['orderable_id'] ?? factory($this->album->class())->create(
                $this->makeAlbum()->toArray()
            )->id,
            'orderable_type' => $properties['orderable_type'] ?? $this->album->class(),
        ]);
    }
}
