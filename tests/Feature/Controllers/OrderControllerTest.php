<?php

namespace Tests\Feature\Controllers;

use CountriesSeeder;
use App\Contracts\OrderRepositoryInterface;
use App\Contracts\OrderItemRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\AccountRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;

class OrderControllerTest extends ControllerTestCase
{
    /**
     * @var $order OrderRepositoryInterface
     */
    protected $order;

    /**
     * @var $order OrderItemRepositoryInterface
     */
    protected $orderItem;

    /**
     * @var $user UserRepositoryInterface
     */
    protected $user;

    /**
     * @var $account AccountRepositoryInterface
     */
    protected $account;

    /**
     * @var $album AlbumRepositoryInterface
     */
    protected $album;

    /**
     * @var $profile ProfileRepositoryInterface
     */
    protected $profile;

    /**
     * @inheritdoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seed(CountriesSeeder::class);

        $this->order = resolve(OrderRepositoryInterface::class);

        $this->orderItem = resolve(OrderItemRepositoryInterface::class);

        $this->user = resolve(UserRepositoryInterface::class);

        $this->account = resolve(AccountRepositoryInterface::class);

        $this->artist = resolve(ArtistRepositoryInterface::class);

        $this->album = resolve(AlbumRepositoryInterface::class);

        $this->profile = resolve(ProfileRepositoryInterface::class);
    }

    /**
     * Define the JSON structure that is expected for the most common responses.
     *
     * @return array
     */
    public function getJsonStructure()
    {
        return [
            'id',
        ];
    }

    /**
     * Ensure that a request for the index returns OK HTTP status and the
     * expected JSON structure.
     */
    public function testAllReturnsOkStatusAndExpectedJsonStructure()
    {
        $this->order->create([]);

        $this->json('GET', route('orders.index'))
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    $this->getJsonStructure()
                ]
            ]);
    }

    /**
     * Ensure that a request for an existing record returns OK HTTP status and
     * the expected JSON structure.
     */
    public function testShowReturnsOkStatusAndExpectedJsonStructure()
    {
        $order = $this->order->create([]);

        $this->json('GET', route('orders.show', ['id' => $order->id]))
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->getJsonStructure()
            ]);
    }

    /**
     * Ensure that Create requests with invalid input result in validation
     * failure HTTP status.
     */
    public function testStoreWithInvalidInputReturnsValidationFailureStatus()
    {
        $this->json('POST', route('orders.store_order'), ['garbage input'])
            ->assertStatus(422);
    }

    /**
     * Ensure that Create requests with one Order Item result in OK HTTP
     * status and the expected JSON structure.
     */
    public function testStoreWithOneOrderItemReturnsOkStatusAndExpectedJsonStructure()
    {
        $orderItem = $this->makeOrderItem()->toArray();

        $this->json('POST', route('orders.store_order'), ['items' => $orderItem])
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => $this->getJsonStructure()
            ]);
    }

    /**
     * Ensure that Create requests with more than one Order Item result in OK HTTP
     * status and the expected JSON structure.
     */
    public function testStoreWithMultipleOrderItemsReturnsOkStatusAndExpectedJsonStructure()
    {
        $orderItem1 = $this->makeOrderItem()->toArray();

        $orderItem2 = $this->makeOrderItem()->toArray();

        $this->json('POST', route('orders.store_order'), ['items' => [$orderItem1, $orderItem2]])
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => $this->getJsonStructure()
            ]);
    }

    /**
     * Ensure that Update requests with exactly one Order Item results in OK HTTP
     * status and the expected JSON structure.
     */
    public function testUpdateWithOneOrderItemReturnsOkStatusAndExpectedJsonStructure()
    {
        $order = $this->order->create([]);

        $orderItem = $this->makeOrderItem()->toArray();

        $this->json('POST', route('orders.add_items', ['orderId' => $order->id]), ['items' => $orderItem])
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => $this->getJsonStructure()
            ]);
    }

    /**
     * Ensure that Update requests with more than one Order Item result in OK HTTP
     * status and the expected JSON structure.
     */
    public function testUpdateWithMultipleOrderItemsReturnsOkStatusAndExpectedJsonStructure()
    {
        $order = $this->order->create([]);

        $orderItem1 = $this->makeOrderItem()->toArray();

        $orderItem2 = $this->makeOrderItem()->toArray();

        $this->json('POST', route('orders.add_items', ['id' => $order->id]),
                ['items' => [$orderItem1, $orderItem2]]
            )
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => $this->getJsonStructure()
            ]);
    }

    /**
     * Ensure that Update requests with two identical Order Items result in OK HTTP
     * status and the expected JSON structure (this is to ensure that even though
     * adding the same Order Item to the Order more than once is not possible,
     * doing so does not result in an error and the duplicate is simply ignored).
     */
    public function testUpdateWithTwoIdenticalOrderItemsReturnsOkStatusAndExpectedJsonStructure()
    {
        $order = $this->order->create([]);

        $orderItem = $this->makeOrderItem()->toArray();

        $this->json('POST', route('orders.add_items', ['id' => $order->id]),
            ['items' => [$orderItem, $orderItem]]
        )
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => $this->getJsonStructure()
            ]);
    }

    /**
     * Ensure that when a valid ID is supplied, the record is destroyed, and an
     * OK HTTP status is returned, along with the expected JSON structure.
     */
    public function testDestroyWithValidInputReturnsOkStatusAndExpectedJsonStructure()
    {
        $order = factory($this->order->class())->create();

        $this->json('DELETE', route('orders.destroy', ['id' => $order->id]))
            ->assertStatus(200)
            ->assertJsonStructure([]);
    }

    /**
     * Ensure that when a invalid ID is supplied, an Unprocessable Entity HTTP
     * status code is returned.
     */
    public function testDestroyWithInvalidInputReturnsUnprocessableEntityStatus()
    {
        $this->json('DELETE', route('orders.destroy', ['id' => 'foo']))
            ->assertStatus(404);
    }

    /**
     * Ensure that when no ID is supplied, a Method Not Allowed HTTP status code
     * is returned.
     */
    public function testDestroyWithMissingInputReturnsMethodNotAllowedStatus()
    {
        $this->json('DELETE', route('orders.destroy', ['id' => null]))
            ->assertStatus(405);
    }

    /**
     * Ensure that when a valid ID is supplied, the record is destroyed, and an
     * OK HTTP status is returned, along with the expected JSON structure.
     */
    public function testDestroyIndividualOrderItemReturnsOkStatusAndExpectedJsonStructure()
    {
        $order = $this->order->create([]);

        $orderItem = $this->orderItem->create(
            $this->makeOrderItem(['order_id' => $order->id])->toArray()
        );

        $this->json('DELETE', route('orders.remove_items', ['id' => $order->id]), ['items' => $orderItem])
            ->assertStatus(200)
            ->assertJsonStructure([]);
    }

    /**
     * Ensure that when a valid ID is supplied, the record is destroyed, and an
     * OK HTTP status is returned, along with the expected JSON structure.
     */
    public function testDestroyMoreThanOneOrderItemReturnsOkStatusAndExpectedJsonStructure()
    {
        $order = $this->order->create([]);

        $orderItem1 = $this->orderItem->create(
            $this->makeOrderItem(['order_id' => $order->id])->toArray()
        );

        $orderItem2 = $this->orderItem->create(
            $this->makeOrderItem(['order_id' => $order->id])->toArray()
        );

        $this->json('DELETE', route('orders.remove_items', ['id' => $order->id]), ['items' => [$orderItem1, $orderItem2]])
            ->assertStatus(200)
            ->assertJsonStructure([]);
    }

    /**
     * Create a User.
     *
     * @return \App\User
     */
    protected function createUser()
    {
        $user = factory($this->user->class())->make();

        $user = $this->user->create([
            'email' => $user->email,
            'password' => $user->password,
            'account' => factory($this->account->class())->raw()
        ]);

        return $user;
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

        // Use the withSongs factory state.

        $album = factory($this->album->class())
            ->state('withSongs')
            ->make($properties);

        // Cast the songs to an array, too.

        $album['songs'] = $album['songs']->toArray();

        return $album;
    }

    /**
     * Create a Song.
     *
     * @return \App\Song
     */
    protected function createSong()
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
            'orderable_id' => $properties['orderable_id'] ?? $this->album->create(
                    $this->makeAlbum()->toArray()
                )->id,
            'orderable_type' => $properties['orderable_type'] ?? $this->album->class(),
        ]);
    }
}
