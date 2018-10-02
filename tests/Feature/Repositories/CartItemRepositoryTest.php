<?php

namespace Tests\Feature\Repositories;

use CountriesSeeder;
use App\Contracts\CartRepositoryInterface;
use App\Contracts\CartItemRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CartItemRepositoryTest extends RepositoryCrudTestCase
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
     * @var CartRepositoryInterface $cart
     */
    protected $cart;

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

        $this->cart = resolve(CartRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function setRepository()
    {
        $this->repo = resolve(CartItemRepositoryInterface::class);
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

        $cart = $this->cart->create(
            factory($this->cart->class())->make()->toArray()
        );

        $item = factory($this->repo->class())->make([
            'cart_id' => $cart->id,
            'cartable_id' => $album->id,
            'cartable_type' => $this->album->class(),
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

        $cart = $this->cart->create(
            factory($this->cart->class())->make()->toArray()
        );

        $item = $this->repo->create(
            factory($this->repo->class())->make([
                'cart_id' => $cart->id,
                'cartable_id' => $album->id,
                'cartable_type' => $this->album->class(),
            ])->toArray()
        );

        $album = $this->album->create($this->makeAlbum()->toArray());

        $newValue = $album->id;

        $property = 'cartable_id';

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

        $cart = $this->cart->create(
            factory($this->cart->class())->make()->toArray()
        );

        $item = $this->repo->create(
            factory($this->repo->class())->make([
                'cart_id' => $cart->id,
                'cartable_id' => $album->id,
                'cartable_type' => $this->album->class(),
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

        $cart = $this->cart->create(
            factory($this->cart->class())->make()->toArray()
        );

        $item = $this->repo->create(
            factory($this->repo->class())->make([
                'cart_id' => $cart->id,
                'cartable_id' => $album->id,
                'cartable_type' => $this->album->class(),
            ])->toArray()
        );

        $item->delete();

        try {
            $this->repo->findById($cart->id);
        } catch(ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }
}
