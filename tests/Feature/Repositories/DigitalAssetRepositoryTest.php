<?php

namespace Tests\Feature\Repositories;

use App\Contracts\AlbumRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\DigitalAssetRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\SongRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use IndieHD\Velkart\Contracts\Repositories\Eloquent\OrderRepositoryContract;
use IndieHD\Velkart\Contracts\Repositories\Eloquent\ProductRepositoryContract;
use IndieHD\Velkart\Database\Seeders\CountriesSeeder;

class DigitalAssetRepositoryTest extends RepositoryCrudTestCase
{
    use WithFaker;

    /**
     * @var ProfileRepositoryInterface
     */
    protected $profile;

    /**
     * @var ArtistRepositoryInterface
     */
    protected $artist;

    /**
     * @var AlbumRepositoryInterface
     */
    protected $album;

    /**
     * @var SongRepositoryInterface
     */
    protected $song;

    /**
     * @var ProductRepositoryContract
     */
    protected $product;

    /**
     * @var OrderRepositoryContract
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

        $this->product = resolve(ProductRepositoryContract::class);

        $this->order = resolve(OrderRepositoryContract::class);
    }

    /**
     * @inheritdoc
     */
    public function setRepository()
    {
        $this->repo = resolve(DigitalAssetRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function testCreateStoresNewResource()
    {
        $item = $this->makeDigitalAsset();

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
        $item = $this->repo->create($this->makeDigitalAsset()->toArray());

        $album = $this->factory($this->album)->create($this->makeAlbum()->toArray());

        $newValue = $album->id;

        $property = 'asset_id';

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
        $item = $this->repo->create($this->makeDigitalAsset()->toArray());

        $updated = $this->repo->update($item->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function testDeleteDeletesResource()
    {
        $item = $this->repo->create($this->makeDigitalAsset()->toArray());

        $item->delete();

        try {
            $this->repo->findById($item->id);
        } catch (ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Ensure that an Album (Digital Asset) belongs to a Product.
     *
     * @return void
     */
    public function testAlbumBelongsToProduct()
    {
        $album = $this->repo->create($this->makeDigitalAsset()->toArray());

        $this->assertInstanceOf($this->product->modelClass(), $album->product);
    }

    /**
     * Ensure that a sold Album morphs to Asset.
     *
     * @return void
     */
    public function testWhenAlbumSoldItMorphsToAsset()
    {
        $soldAlbum = $this->repo->create($this->makeDigitalAsset()->toArray());

        $this->assertInstanceOf($this->album->class(), $soldAlbum->asset);
    }

    /**
     * Ensure that a Song (Digital Asset) belongs to a Product.
     *
     * @return void
     */
    public function testSongBelongsToProduct()
    {
        $properties = [
            'asset_id' => $this->factory($this->album)->create()->songs->first()->id,
            'asset_type' => $this->song->class(),
        ];

        $song = $this->repo->create($this->makeDigitalAsset($properties)->toArray());

        $this->assertInstanceOf($this->product->modelClass(), $song->product);
    }

    /**
     * Ensure that a sold Song morphs to Asset.
     *
     * @return void
     */
    public function testWhenSongSoldItMorphsToAsset()
    {
        $song = $this->createSong();

        $soldSong = $this->repo->create($this->makeDigitalAsset([
            'asset_id' => $song->id,
            'asset_type' => $this->song->class(),
        ])->toArray());

        $this->assertInstanceOf($this->song->class(), $soldSong->asset);
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
            $this->factory($this->artist)->raw(
                $this->factory($this->profile)->raw()
            )
        );

        // This is the one property that can't be passed via the argument.

        $properties['artist_id'] = $artist->id;

        return $this->factory($this->album)->create($properties);
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
            $this->factory($this->artist)->raw(
                $this->factory($this->profile)->raw()
            )
        );

        // This is the one property that can't be passed via the argument.

        $properties['artist_id'] = $artist->id;

        return $this->factory($this->album)->make($properties);
    }

    /**
     * Create a Song.
     *
     * @return \App\Song
     */
    protected function createSong()
    {
        $album = $this->factory($this->album)->create();

        return $album->songs()->first();
    }

    /**
     * Make a Digital Asset.
     *
     * @param array $properties
     * @return \App\DigitalAsset
     */
    protected function makeDigitalAsset($properties = [])
    {
        $this->setUpFaker();

        $title = $this->faker->title;

        $product = resolve(ProductRepositoryContract::class)->create([
            'name' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->words(5, true),
            'price' => $this->faker->numberBetween(10, 20),
            'sku' => $this->faker->unique()->numberBetween(1111111, 999999),
            'cover' => UploadedFile::fake()->image('product.png', 600, 600),
            'quantity' => 10,
            'status' => 1,
        ]);

        return $this->factory()->make([
            'product_id' => $properties['product_id'] ?? $product->id,
            'asset_id' => $properties['asset_id'] ?? $this->factory($this->album)->create(
                $this->makeAlbum()->toArray()
            )->id,
            'asset_type' => $properties['asset_type'] ?? $this->album->class(),
        ]);
    }
}
