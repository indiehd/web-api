<?php

namespace Tests\Feature\Repositories;

use App\OrderItem;
use Ramsey\Uuid\Uuid;
use CountriesSeeder;
use App\Contracts\OrderRepositoryInterface;
use App\Contracts\OrderItemRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderRepositoryTest extends RepositoryCrudTestCase
{
    /**
     * @var OrderItemRepositoryInterface $orderItem
     */
    protected $orderItem;

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
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        $this->seed(CountriesSeeder::class);

        $this->orderItem = resolve(OrderItemRepositoryInterface::class);

        $this->profile = resolve(ProfileRepositoryInterface::class);

        $this->album = resolve(AlbumRepositoryInterface::class);

        $this->artist = resolve(ArtistRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function setRepository()
    {
        $this->repo = resolve(OrderRepositoryInterface::class);
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
                factory($this->profile->class())->make()->toArray()
            )->toArray()
        );

        // This is the one property that can't passed via the argument.

        $properties['artist_id'] = $artist->id;

        return factory($this->album->class())->make($properties);
    }

    /**
     * Makes a new Order Item.
     *
     * @return \App\OrderItem
     */
    public function makeOrderItem()
    {
        $album = $this->album->create($this->makeAlbum()->toArray());

        $order = $this->repo->create(
            factory($this->repo->class())->make()->toArray()
        );

        return factory($this->orderItem->class())->make([
            'order_id' => $order->id,
            'orderable_id' => $album->id,
            'orderable_type' => $this->album->class(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function test_method_create_storesNewResource()
    {
        $order = factory($this->repo->class())->make();

        $this->assertInstanceOf(
            $this->repo->class(),
            $this->repo->create($order->toArray())
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_updatesResource()
    {
        $order = $this->repo->create(
            factory($this->repo->class())->make()->toArray()
        );

        $newValue = Uuid::uuid4()->toString();

        $property = 'uuid';

        $this->repo->update($order->id, [
            $property => $newValue,
        ]);

        $this->assertTrue(
            $this->repo->findById($order->id)->{$property} === $newValue
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_returnsModelInstance()
    {
        $order = $this->repo->create(
            factory($this->repo->class())->make()->toArray()
        );

        $updated = $this->repo->update($order->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function test_method_delete_deletesResource()
    {
        $order = $this->repo->create(
            factory($this->repo->class())->make()->toArray()
        );

        $order->delete();

        try {
            $this->repo->findById($order->id);
        } catch(ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Ensure that when an Order Item is associated with an Order, the Order has
     * one or more Order Items.
     *
     * @return void
     */
    public function test_items_whenItemsAssociatedWithOrder_orderHasManyItems()
    {
        $item = $this->orderItem->create($this->makeOrderItem()->toArray());

        $this->assertInstanceOf($this->orderItem->class(), $item->order->items()->first());
    }
}
