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
    public function setUp()
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
     * Creates a Song.
     *
     * @return \App\Song
     */
    public function createSong()
    {
        $album = $this->album->create(
            factory($this->album->class())->raw()
        );

        return $this->repo->create(
            factory($this->repo->class())->raw([
                'album_id' => $album->id,
                'track_number' => 1,
            ])
        );
    }

    /**
     * Makes a new Order Item.
     *
     * @return \App\OrderItem
     */
    public function makeOrderItem($properties = [])
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

    /**
     * @inheritdoc
     */
    public function test_method_create_storesNewResource()
    {
        $this->assertInstanceOf(
            $this->repo->class(),
            $this->createSong()
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_updatesResource()
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
    public function test_method_update_returnsModelInstance()
    {
        $song = $this->createSong();

        $updated = $this->repo->update($song->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function test_method_delete_deletesResource()
    {
        $song = $this->createSong();

        $song->delete();

        try {
            $this->repo->findById($song->id);
        } catch(ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Ensure that when a Song is associated with an Album, the Song belongs to
     * an Album.
     *
     * @return void
     */
    public function test_albums_song_belongsToAlbum()
    {
        $this->assertInstanceOf($this->album->class(), $this->createSong()->album);
    }

    /**
     * Ensure that when a Song is associated with a FlacFile, the Song belongs
     * to a FlacFile.
     *
     * @return void
     */
    public function test_flacFile_song_belongsToFlacFile()
    {
        $this->assertInstanceOf($this->flacFile->class(), $this->createSong()->flacFile);
    }

    /**
     * Ensure that when a copy of a Song is sold, the Song morphs many Order
     * Items.
     *
     * @return void
     */
    public function test_copiesSold_whenSongSold_morphsManyOrderItems()
    {
        $song = $this->createSong();

        $this->orderItem->create($this->makeOrderItem([
            'orderable_id' => $song->id,
            'orderable_type' => $this->repo->class(),
        ])->toArray());

        $this->assertInstanceOf($this->orderItem->class(), $song->copiesSold->first());
    }
}
