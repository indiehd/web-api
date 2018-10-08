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
     * @var $album AlbumRepositoryInterface
     */
    protected $album;

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
    public function setUp()
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
     * Creates a new Album.
     *
     * @param array $properties
     * @return \App\Album
     */
    public function makeAlbum(array $properties = [])
    {
        $artist = $this->artist->create(
            factory($this->artist->class())->make(
                factory($this->profile->class())->raw()
            )->toArray()
        );

        // This is the one property that can't passed via the argument.

        $properties['artist_id'] = $artist->id;

        return factory($this->repo->class())->make($properties);
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
            'orderable_id' => $properties['orderable_id'] ?? $this->repo->create(
                    $this->makeAlbum()->toArray()
                )->id,
            'orderable_type' => $properties['orderable_type'] ?? $this->repo->class(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function test_method_create_storesNewResource()
    {
        $this->assertInstanceOf(
            $this->repo->class(),
            $this->repo->create($this->makeAlbum()->toArray())
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_updatesResource()
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
    public function test_method_update_returnsModelInstance()
    {
        $album = $this->repo->create($this->makeAlbum()->toArray());

        $updated = $this->repo->update($album->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function test_method_delete_deletesResource()
    {
        $album = $this->repo->create($this->makeAlbum()->toArray());

        $album->delete();

        try {
            $this->repo->findById($album->id);
        } catch(ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Ensure that a newly-created Album belongs to an Artist.
     *
     * @return void
     */
    public function test_artist_albumBelongsToArtist()
    {
        $this->assertInstanceOf($this->artist->class(), $this->makeAlbum()->artist);
    }

    /**
     * Ensure that a newly-created Album has one or more Songs.
     *
     * @return void
     */
    public function test_songs_albumHasManySongs()
    {
        $album = $this->repo->create($this->makeAlbum()->toArray());

        factory($this->song->class())->create([
            'track_number' => 1,
            'album_id' => $album->id
        ]);

        $this->assertInstanceOf($this->song->class(), $album->songs->first());
    }

    /**
     * Ensure that a newly-created Album belongs to one or more Genres.
     *
     * @return void
     */
    public function test_genres_albumBelongsToManyGenres()
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
    public function test_copiesSold_whenAlbumSold_morphsManyOrderItems()
    {
        $album = $this->repo->create($this->makeAlbum()->toArray());

        $this->orderItem->create($this->makeOrderItem([
            'orderable_id' => $album->id,
            'orderable_type' => $this->repo->class(),
        ])->toArray());

        $this->assertInstanceOf($this->orderItem->class(), $album->copiesSold->first());
    }
}
