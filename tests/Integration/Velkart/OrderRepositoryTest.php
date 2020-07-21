<?php

namespace Tests\Integration\Velkart;

use App\Contracts\AccountRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\DigitalAssetRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use CountriesSeeder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use IndieHD\Velkart\Contracts\Repositories\Eloquent\OrderRepositoryContract;
use IndieHD\Velkart\Contracts\Repositories\Eloquent\ProductRepositoryContract;
use Tests\TestCase;

class OrderRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var OrderRepositoryContract $repo
     */
    protected $repo;

    /**
     * @var UserRepositoryInterface $user
     */
    protected $user;

    /**
     * @var AccountRepositoryInterface $account
     */
    protected $account;

    /**
     * @var DigitalAssetRepositoryInterface $digitalAsset
     */
    protected $digitalAsset;

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
     * @var ProductRepositoryContract $product
     */
    protected $product;

    /**
     * @inheritdoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->setRepository();

        $this->seed(CountriesSeeder::class);

        $this->user = resolve(UserRepositoryInterface::class);

        $this->account = resolve(AccountRepositoryInterface::class);

        $this->digitalAsset = resolve(DigitalAssetRepositoryInterface::class);

        $this->profile = resolve(ProfileRepositoryInterface::class);

        $this->artist = resolve(ArtistRepositoryInterface::class);

        $this->album = resolve(AlbumRepositoryInterface::class);

        $this->product = resolve(ProductRepositoryContract::class);
    }

    /**
     * @inheritdoc
     */
    public function setRepository()
    {
        $this->repo = resolve(OrderRepositoryContract::class);
    }

    /**
     * @inheritdoc
     */
    public function testCreateStoresNewResource()
    {
        $order = factory($this->repo->modelClass())->make();

        $this->assertInstanceOf(
            $this->repo->modelClass(),
            $this->repo->create($order->toArray())
        );
    }

    /**
     * @inheritdoc
     */
    public function testUpdateUpdatesResource()
    {
        $order = $this->repo->create(
            factory($this->repo->modelClass())->raw()
        );

        $newValue = $this->createUser()->id;

        $property = 'customer_id';

        $this->repo->update($order->id, [
            $property => $newValue,
        ]);

        #dd($this->repo->findById($order->id));

        $this->assertTrue(
            $this->repo->findById($order->id)->{$property} == $newValue
        );
    }

    /**
     * @inheritdoc
     */
    public function testUpdateReturnsModelInstance()
    {
        $order = $this->repo->create(
            factory($this->repo->modelClass())->raw()
        );

        $updated = $this->repo->update($order->id, []);

        $this->assertTrue($updated);
    }

    /**
     * @inheritdoc
     */
    public function testDeleteDeletesResource()
    {
        $order = $this->repo->create(
            factory($this->repo->modelClass())->raw()
        );

        $order->delete();

        try {
            $this->repo->findById($order->id);
        } catch (ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Ensure that when an Digital Asset is related to a Product, the Product has
     * one or more Digital Assets.
     *
     * @return void
     */
    public function testWhenDigitalAssetRelatedToProductItBelongsToProduct()
    {
        $asset = $this->digitalAsset->create($this->makeDigitalAsset()->toArray());

        $this->assertInstanceOf($this->product->modelClass(), $asset->product);
    }

    /**
     * Create a User.
     *
     * @param array $userProperties
     * @param array $accountProperties
     * @return \App\User
     */
    protected function createUser(array $userProperties = [], array $accountProperties = [])
    {
        $user = factory($this->user->class())->make($userProperties);

        $account = factory($this->account->class())->make($accountProperties);

        $user = $this->user->create([
            'email' => $user->email,
            'name' => $user->name,
            'password' => $user->password,
            'account' => $account->toArray(),
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

        return factory($this->album->class())->make($properties);
    }

    /**
     * Makes a Digital Asset.
     *
     * @return \App\DigitalAsset
     */
    protected function makeDigitalAsset()
    {
        $album = factory($this->album->class())->create($this->makeAlbum()->toArray());

        #$order = $this->repo->create(
        #    factory($this->repo->modelClass())->raw()
        #);

        return factory($this->digitalAsset->class())->make([
            #'product_id' => $order->id,
            'asset_id' => $album->id,
            'asset_type' => $this->album->class(),
        ]);
    }
}
