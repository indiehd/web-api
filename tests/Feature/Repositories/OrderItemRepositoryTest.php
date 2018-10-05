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
     * @var SongRepositoryInterface $song
     */
    protected $song;

    /**
     * @var AlbumRepositoryInterface $album
     */
    protected $album;

    /**
     * @var OrderRepositoryInterface $order
     */
    protected $order;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        $this->seed(CountriesSeeder::class);

        $this->profile = resolve(ProfileRepositoryInterface::class);

        $this->album = resolve(AlbumRepositoryInterface::class);

        $this->song = resolve(SongRepositoryInterface::class);

        $this->artist = resolve(ArtistRepositoryInterface::class);

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
     * Makes a new Album.
     *
     * @param array $properties
     * @return \App\Album
     */
    public function makeAlbum(array $properties = [])
    {
        $artist = $this->artist->create(
            factory($this->artist->class())->make(
                factory($this->profile->class())->raw()
            )->toArray()
        );

        // This is the one property that can't passed via the argument.

        $properties['artist_id'] = $artist->id;

        return factory($this->album->class())->make($properties);
    }

    /**
     * Creates a Song.
     *
     * @return \App\Song
     */
    public function createSong()
    {
        $album = $this->album->create(
            factory($this->album->class())->raw()
        );

        return $this->song->create(
            factory($this->song->class())->raw([
                'album_id' => $album->id,
                'track_number' => 1,
            ])
        );
    }

    /**
     * Makes a new Order Item.
     *
     * @return \App\OrderItem
     */
    public function makeOrderItem($properties = [])
    {
        return factory($this->repo->class())->make([
            'order_id' => $properties['order_id'] ?? $this->order->create(
                factory($this->order->class())->raw()
            )->id,
            'orderable_id' => $properties['orderable_id'] ?? $this->album->create(
                $this->makeAlbum()->toArray()
            )->id,
            'orderable_type' => $properties['orderable_type'] ?? $this->album->class(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function test_method_create_storesNewResource()
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
    public function test_method_update_updatesResource()
    {
        $item = $this->repo->create($this->makeOrderItem()->toArray());

        $album = $this->album->create($this->makeAlbum()->toArray());

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
    public function test_method_update_returnsModelInstance()
    {
        $item = $this->repo->create($this->makeOrderItem()->toArray());

        $updated = $this->repo->update($item->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function test_method_delete_deletesResource()
    {
        $item = $this->repo->create($this->makeOrderItem()->toArray());

        $item->delete();

        try {
            $this->repo->findById($item->id);
        } catch(ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Ensure that when an Order is associated with an Order Item, the Order
     * Item belongs to an Order.
     *
     * @return void
     */
    public function test_whenAssociatedWithOrder_OrderItemBelongsToOrder()
    {
        $item = $this->repo->create($this->makeOrderItem()->toArray());

        $this->assertInstanceOf($this->order->class(), $item->order);
    }

    /**
     * Ensure that a sold copy of an Album morphs to Orderable.
     *
     * @return void
     */
    public function test_orderable_soldAlbum_morphsToOrderable()
    {
        $soldAlbum = $this->repo->create($this->makeOrderItem()->toArray());

        $this->assertInstanceOf($this->album->class(), $soldAlbum->orderable);
    }

    /**
     * Ensure that a sold copy of a Song morphs to Orderable.
     *
     * @return void
     */
    public function test_orderable_soldSong_morphsToOrderable()
    {
        $song = $this->createSong();

        $soldSong = $this->repo->create($this->makeOrderItem([
            'orderable_id' => $song->id,
            'orderable_type' => $this->song->class(),
        ])->toArray());

        $this->assertInstanceOf($this->song->class(), $soldSong->orderable);
    }
}
