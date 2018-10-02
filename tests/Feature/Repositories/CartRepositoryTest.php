<?php

namespace Tests\Feature\Repositories;

use Ramsey\Uuid\Uuid;
use CountriesSeeder;
use App\Contracts\CartRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CartRepositoryTest extends RepositoryCrudTestCase
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
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        $this->seed(CountriesSeeder::class);

        $this->profile = resolve(ProfileRepositoryInterface::class);

        $this->album = resolve(AlbumRepositoryInterface::class);

        $this->artist = resolve(ArtistRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function setRepository()
    {
        $this->repo = resolve(CartRepositoryInterface::class);
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
        $cart = factory($this->repo->class())->make();

        $this->assertInstanceOf(
            $this->repo->class(),
            $this->repo->create($cart->toArray())
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_updatesResource()
    {
        $cart = $this->repo->create(
            factory($this->repo->class())->make()->toArray()
        );

        $newValue = Uuid::uuid1()->toString();

        $property = 'uuid';

        $this->repo->update($cart->id, [
            $property => $newValue,
        ]);

        $this->assertTrue(
            $this->repo->findById($cart->id)->{$property} === $newValue
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_returnsModelInstance()
    {
        $cart = $this->repo->create(
            factory($this->repo->class())->make()->toArray()
        );

        $updated = $this->repo->update($cart->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function test_method_delete_deletesResource()
    {
        $cart = $this->repo->create(
            factory($this->repo->class())->make()->toArray()
        );

        $cart->delete();

        try {
            $this->repo->findById($cart->id);
        } catch(ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }
}
