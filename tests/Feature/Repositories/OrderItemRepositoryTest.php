<?php

namespace Tests\Feature\Repositories;

use CountriesSeeder;
use App\Contracts\OrderRepositoryInterface;
use App\Contracts\OrderItemRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
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
                factory($this->profile->class())->make()->toArray()
            )->toArray()
        );

        // This is the one property that can't passed via the argument.

        $properties['artist_id'] = $artist->id;

        return factory($this->album->class())->make($properties);
    }

    /**
     * @inheritdoc
     */
    public function test_method_create_storesNewResource()
    {
        $album = $this->album->create($this->makeAlbum()->toArray());

        $order = $this->order->create(
            factory($this->order->class())->make()->toArray()
        );

        $item = factory($this->repo->class())->make([
            'order_id' => $order->id,
            'orderable_id' => $album->id,
            'orderable_type' => $this->album->class(),
        ]);

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
        $album = $this->album->create($this->makeAlbum()->toArray());

        $order = $this->order->create(
            factory($this->order->class())->make()->toArray()
        );

        $item = $this->repo->create(
            factory($this->repo->class())->make([
                'order_id' => $order->id,
                'orderable_id' => $album->id,
                'orderable_type' => $this->album->class(),
            ])->toArray()
        );

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
        $album = $this->album->create($this->makeAlbum()->toArray());

        $order = $this->order->create(
            factory($this->order->class())->make()->toArray()
        );

        $item = $this->repo->create(
            factory($this->repo->class())->make([
                'order_id' => $order->id,
                'orderable_id' => $album->id,
                'orderable_type' => $this->album->class(),
            ])->toArray()
        );

        $updated = $this->repo->update($item->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function test_method_delete_deletesResource()
    {
        $album = $this->album->create($this->makeAlbum()->toArray());

        $order = $this->order->create(
            factory($this->order->class())->make()->toArray()
        );

        $item = $this->repo->create(
            factory($this->repo->class())->make([
                'order_id' => $order->id,
                'orderable_id' => $album->id,
                'orderable_type' => $this->album->class(),
            ])->toArray()
        );

        $item->delete();

        try {
            $this->repo->findById($order->id);
        } catch(ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }
}
