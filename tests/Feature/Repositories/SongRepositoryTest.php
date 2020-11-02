<?php

namespace Tests\Feature\Repositories;

use App\Contracts\AlbumRepositoryInterface;
use App\Contracts\DigitalAssetRepositoryInterface;
use App\Contracts\FlacFileRepositoryInterface;
use App\Contracts\SongRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use IndieHD\Velkart\Contracts\Repositories\Eloquent\OrderRepositoryContract;
use IndieHD\Velkart\Contracts\Repositories\Eloquent\OrderStatusRepositoryContract;

class SongRepositoryTest extends RepositoryCrudTestCase
{
    /**
     * @var AlbumRepositoryInterface $album
     */
    protected $album;

    /**
     * @var FlacFileRepositoryInterface $flacFile
     */
    protected $flacFile;

    /**
     * @var OrderStatusRepositoryContract $orderStatus
     */
    protected $orderStatus;

    /**
     * @var OrderRepositoryContract $order
     */
    protected $order;

    /**
     * @var DigitalAssetRepositoryInterface $digitalAsset
     */
    protected $digitalAsset;

    /**
     * @inheritdoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seed('CountriesSeeder');

        $this->album = resolve(AlbumRepositoryInterface::class);

        $this->flacFile = resolve(FlacFileRepositoryInterface::class);

        $this->orderStatus = resolve(OrderStatusRepositoryContract::class);

        $this->order = resolve(OrderRepositoryContract::class);

        $this->digitalAsset = resolve(DigitalAssetRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function setRepository()
    {
        $this->repo = resolve(SongRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function testCreateStoresNewResource()
    {
        $this->assertInstanceOf(
            $this->repo->class(),
            $this->createSong()
        );
    }

    /**
     * @inheritdoc
     */
    public function testUpdateUpdatesResource()
    {
        $song = $this->createSong();

        $newValue = 'Foo Bar';

        $property = 'name';

        $this->repo->update($song->id, [
            $property => $newValue,
        ]);

        $this->assertTrue(
            $this->repo->findById($song->id)->{$property} === $newValue
        );
    }

    /**
     * @inheritdoc
     */
    public function testUpdateReturnsModelInstance()
    {
        $song = $this->createSong();

        $updated = $this->repo->update($song->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function testDeleteDeletesResource()
    {
        $song = $this->createSong();

        $song->delete();

        try {
            $this->repo->findById($song->id);
        } catch (ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Ensure that when a Song is related to an Album, the Song belongs to
     * an Album.
     *
     * @return void
     */
    public function testSongBelongsToAlbum()
    {
        $this->assertInstanceOf($this->album->class(), $this->createSong()->album);
    }

    /**
     * Ensure that when a Song is related to a FlacFile, the Song belongs
     * to a FlacFile.
     *
     * @return void
     */
    public function testSongBelongsToFlacFile()
    {
        $this->assertInstanceOf($this->flacFile->class(), $this->createSong()->flacFile);
    }

    /**
     * Ensure that when a Song is sold, the Song morphs many Order Items.
     *
     * @return void
     */
    public function testWhenSongSoldItMorphsManyDigitalAsset()
    {
        $song = $this->createSong();

        $this->digitalAsset->create($this->makeDigitalAsset([
            'asset_id' => $song->id,
            'asset_type' => $this->repo->class(),
        ])->toArray());

        $status = $this->orderStatus->create(['name' => 'Completed']);

        $order = $this->order->create(['order_status_id' => $status->id]);

        $order->products()->attach($song->asset->product, ['price' => $song->asset->product->price]);

        $this->assertInstanceOf($this->digitalAsset->class(), $song->copiesSold->first());
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
        return $this->factory($this->digitalAsset)->make([
            'asset_id' => $properties['asset_id'] ?? $this->album->create(
                // TODO This method doesn't exist here; add it.
                $this->makeAlbum()->toArray()
            )->id,
            'asset_type' => $properties['asset_type'] ?? $this->album->class(),
        ]);
    }
}
