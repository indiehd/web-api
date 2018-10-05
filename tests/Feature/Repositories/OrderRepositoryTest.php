<?php

namespace Tests\Feature\Repositories;

use App\OrderItem;
use CountriesSeeder;
use App\Contracts\AccountRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\OrderRepositoryInterface;
use App\Contracts\OrderItemRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderRepositoryTest extends RepositoryCrudTestCase
{
    /**
     * @var UserRepositoryInterface $user
     */
    protected $user;

    /**
     * @var AccountRepositoryInterface $account
     */
    protected $account;

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

        $this->user = resolve(UserRepositoryInterface::class);

        $this->account = resolve(AccountRepositoryInterface::class);

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
     * Create a new User object.
     *
     * @param array $userProperties
     * @param array $accountProperties
     * @return \App\User
     */
    public function createUser(array $userProperties = [], array $accountProperties = [])
    {
        $user = factory($this->user->class())->make($userProperties);

        $account = factory($this->account->class())->make($accountProperties);

        $user = $this->user->create([
            'username' => $user->username,
            'password' => $user->password,
            'account' => $account->toArray(),
        ]);

        return $user;
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
            factory($this->artist->class())->raw(
                factory($this->profile->class())->raw()
            )
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
            factory($this->repo->class())->raw()
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
            factory($this->repo->class())->raw()
        );

        $newValue = $this->createUser()->id;

        $property = 'user_id';

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
            factory($this->repo->class())->raw()
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
            factory($this->repo->class())->raw()
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
