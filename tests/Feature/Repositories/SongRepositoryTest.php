<?php

namespace Tests\Feature\Repositories;

use App\Contracts\SongRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;
use App\Contracts\FlacFileRepositoryInterface;
use App\Contracts\OrderRepositoryInterface;
use App\Contracts\OrderItemRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
     * @var OrderRepositoryInterface $order
     */
    protected $order;

    /**
     * @var OrderItemRepositoryInterface $orderItem
     */
    protected $orderItem;

    /**
     * @inheritdoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seed('CountriesSeeder');

        $this->album = resolve(AlbumRepositoryInterface::class);

        $this->flacFile = resolve(FlacFileRepositoryInterface::class);

        $this->order = resolve(OrderRepositoryInterface::class);

        $this->orderItem = resolve(OrderItemRepositoryInterface::class);
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
    public function testWhenSongSoldItMorphsManyOrderItems()
    {
        $song = $this->createSong();

        $this->orderItem->create($this->makeOrderItem([
            'orderable_id' => $song->id,
            'orderable_type' => $this->repo->class(),
        ])->toArray());

        $this->assertInstanceOf($this->orderItem->class(), $song->copiesSold->first());
    }

    /**
     * Create a Song.
     *
     * @return \App\Song
     */
    protected function createSong()
    {
        $album = factory($this->album->class())->create();

        return $album->songs()->first();
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
                // TODO This method doesn't exist here; add it.
                $this->makeAlbum()->toArray()
            )->id,
            'orderable_type' => $properties['orderable_type'] ?? $this->album->class(),
        ]);
    }
}
