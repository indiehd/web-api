<?php

namespace Tests\Feature\Repositories;

use App\Contracts\AlbumRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\DigitalAssetRepositoryInterface;
use App\Contracts\GenreRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\SongRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use IndieHD\Velkart\Contracts\Repositories\Eloquent\OrderRepositoryContract;
use IndieHD\Velkart\Contracts\Repositories\Eloquent\OrderStatusRepositoryContract;
use IndieHD\Velkart\Contracts\Repositories\Eloquent\ProductRepositoryContract;
use IndieHD\Velkart\Database\Seeders\CountriesSeeder;

class AlbumRepositoryTest extends RepositoryCrudTestCase
{
    /**
     * @var ArtistRepositoryInterface
     */
    protected $artist;

    /**
     * @var ProfileRepositoryInterface
     */
    protected $profile;

    /**
     * @var SongRepositoryInterface
     */
    protected $song;

    /**
     * @var GenreRepositoryInterface
     */
    protected $genre;

    /**
     * @var ProductRepositoryContract
     */
    protected $product;

    /**
     * @var OrderStatusRepositoryContract
     */
    protected $orderStatus;

    /**
     * @var OrderRepositoryContract
     */
    protected $order;

    /**
     * @var DigitalAssetRepositoryInterface
     */
    protected $digitalAsset;

    /**
     * @inheritdoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seed(CountriesSeeder::class);

        $this->artist = resolve(ArtistRepositoryInterface::class);

        $this->profile = resolve(ProfileRepositoryInterface::class);

        $this->song = resolve(SongRepositoryInterface::class);

        $this->genre = resolve(GenreRepositoryInterface::class);

        $this->product = resolve(ProductRepositoryContract::class);

        $this->orderStatus = resolve(OrderStatusRepositoryContract::class);

        $this->order = resolve(OrderRepositoryContract::class);

        $this->digitalAsset = resolve(DigitalAssetRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function setRepository()
    {
        $this->repo = resolve(AlbumRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function testCreateStoresNewResource()
    {
        $this->assertInstanceOf(
            $this->repo->class(),
            $this->repo->create($this->makeAlbum()->toArray())
        );
    }

    /**
     * @inheritdoc
     */
    public function testUpdateUpdatesResource()
    {
        $album = $this->repo->create($this->makeAlbum()->toArray());

        $newTitle = 'Foo Bar';

        $this->repo->update($album->id, [
            'title' => $newTitle,
        ]);

        $this->assertTrue(
            $this->repo->findById($album->id)->title === $newTitle
        );
    }

    /**
     * @inheritdoc
     */
    public function testUpdateReturnsModelInstance()
    {
        $album = $this->repo->create($this->makeAlbum()->toArray());

        $updated = $this->repo->update($album->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function testDeleteDeletesResource()
    {
        $album = $this->repo->create($this->makeAlbum()->toArray());

        $album->delete();

        try {
            $this->repo->findById($album->id);
        } catch (ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Ensure that an Album belongs to an Artist.
     *
     * @return void
     */
    public function testAlbumBelongsToArtist()
    {
        $this->assertInstanceOf($this->artist->class(), $this->makeAlbum()->artist);
    }

    /**
     * Ensure that an Album has one or more Songs.
     *
     * @return void
     */
    public function testAlbumHasManySongs()
    {
        $album = $this->repo->create($this->makeAlbum()->toArray());

        $this->assertInstanceOf($this->song->class(), $album->songs->first());
    }

    /**
     * Ensure that an Album belongs to one or more Genres.
     *
     * @return void
     */
    public function testAlbumBelongsToManyGenres()
    {
        $album = $this->repo->create($this->makeAlbum()->toArray());

        $genre = $this->genre->create($this->factory($this->genre)->raw());

        $album->genres()->attach($genre->id);

        $this->assertInstanceOf($this->genre->class(), $album->genres->first());
    }

    /**
     * Ensure that when a copy of an Album is sold, the Album morphs many
     * Digital Assets.
     *
     * @return void
     */
    public function testWhenAlbumSoldItMorphsManyDigitalAsset()
    {
        $album = $this->repo->create($this->makeAlbum()->toArray());

        $this->digitalAsset->create($this->makeDigitalAsset([
            'asset_id' => $album->id,
            'asset_type' => $this->repo->class(),
        ])->toArray());

        $status = $this->orderStatus->create(['name' => 'Completed']);

        $order = $this->order->create(['order_status_id' => $status->id]);

        $order->products()->attach($album->asset->product, ['price' => $album->asset->product->price]);

        $this->assertInstanceOf($this->digitalAsset->class(), $album->copiesSold->first());
    }

    /**
     * Create an Album.
     *
     * @param array $properties
     * @return \App\Album
     */
    protected function makeAlbum(array $properties = [])
    {
        $artist = $this->artist->create(
            $this->factory($this->artist)->make(
                $this->factory($this->profile)->raw()
            )->toArray()
        );

        // This is the one property that can't be passed via the argument.

        $properties['artist_id'] = $artist->id;

        // Use the withSongs factory state.

        $album = $this->factory()
            ->withSongs()
            ->make($properties);

        // Cast the songs to an array, too.

        $album['songs'] = $album['songs']->toArray();

        return $album;
    }

    /**
     * Make a Digital Asset.
     *
     * @param array $properties
     * @return \App\DigitalAsset
     */
    protected function makeDigitalAsset(array $properties = [])
    {
        return $this->factory($this->digitalAsset)->make([
            'asset_id' => $properties['asset_id'] ?? $this->repo->create(
                $this->makeAlbum()->toArray()
            )->id,
            'asset_type' => $properties['asset_type'] ?? $this->repo->class(),
        ]);
    }
}
