<?php

namespace Tests\Feature\Repositories;

use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;
use App\Contracts\GenreRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\SongRepositoryInterface;
use App\Contracts\OrderRepositoryInterface;
use App\Contracts\OrderItemRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AlbumRepositoryTest extends RepositoryCrudTestCase
{
    /**
     * @var $artist ArtistRepositoryInterface
     */
    protected $artist;

    /**
     * @var $profile ProfileRepositoryInterface
     */
    protected $profile;

    /**
     * @var $song SongRepositoryInterface
     */
    protected $song;

    /**
     * @var $genre GenreRepositoryInterface
     */
    protected $genre;

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

        $this->artist = resolve(ArtistRepositoryInterface::class);

        $this->profile = resolve(ProfileRepositoryInterface::class);

        $this->song = resolve(SongRepositoryInterface::class);

        $this->genre = resolve(GenreRepositoryInterface::class);

        $this->order = resolve(OrderRepositoryInterface::class);

        $this->orderItem = resolve(OrderItemRepositoryInterface::class);
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

        $genre = $this->genre->create(factory($this->genre->class())->raw());

        $album->genres()->attach($genre->id);

        $this->assertInstanceOf($this->genre->class(), $album->genres->first());
    }

    /**
     * Ensure that when a copy of an Album is sold, the Album morphs many Order
     * Items.
     *
     * @return void
     */
    public function testWhenAlbumSoldItMorphsManyOrderItem()
    {
        $album = $this->repo->create($this->makeAlbum()->toArray());

        $this->orderItem->create($this->makeOrderItem([
            'orderable_id' => $album->id,
            'orderable_type' => $this->repo->class(),
        ])->toArray());

        $this->assertInstanceOf($this->orderItem->class(), $album->copiesSold->first());
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
            factory($this->artist->class())->make(
                factory($this->profile->class())->raw()
            )->toArray()
        );

        // This is the one property that can't be passed via the argument.

        $properties['artist_id'] = $artist->id;

        // Use the withSongs factory state.

        $album = factory($this->repo->class())
            ->state('withSongs')
            ->make($properties);

        // Cast the songs to an array, too.

        $album['songs'] = $album['songs']->toArray();

        return $album;
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
            'orderable_id' => $properties['orderable_id'] ?? $this->repo->create(
                $this->makeAlbum()->toArray()
            )->id,
            'orderable_type' => $properties['orderable_type'] ?? $this->repo->class(),
        ]);
    }
}
