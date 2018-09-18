<?php

namespace Tests\Feature\Repositories;

use DB;

use CountriesSeeder;
use App\Contracts\SkuRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;
use App\Contracts\SongRepositoryInterface;

class SkuRepositoryTest extends RepositoryCrudTestCase
{
    /**
     * @var ArtistRepositoryInterface $artist
     */
    protected $artist;

    /**
     * @var AlbumRepositoryInterface $album
     */
    protected $album;

    /**
     * @var SongRepositoryInterface $song
     */
    protected $song;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        $this->seed(CountriesSeeder::class);

        $this->artist = resolve(ArtistRepositoryInterface::class);

        $this->album = resolve(AlbumRepositoryInterface::class);

        $this->song = resolve(SongRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function setRepository()
    {
        $this->repo = resolve(SkuRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function test_method_create_storesNewResource()
    {
        $flacFile = factory($this->repo->class())->make()->toArray();

        $this->assertInstanceOf(
            $this->repo->class(),
            $this->repo->create($flacFile)
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_updatesResource()
    {
        $sku = factory($this->repo->class())->create();

        $newValue = 'Foo Bar';

        $property = 'description';

        $this->repo->update($sku->id, [
            $property => $newValue,
        ]);

        $this->assertTrue(
            $this->repo->findById($sku->id)->{$property} === $newValue
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_returnsModelInstance()
    {
        $sku = factory($this->repo->class())->create();

        $updated = $this->repo->update($sku->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function test_method_delete_deletesResource()
    {
        $sku = factory($this->repo->class())->create();

        $sku->delete();

        $this->assertNull($this->repo->findById($sku->id));
    }

    /**
     * Ensure that when a Song is associated with a Sku, the Sku has many Songs.
     *
     * @return void
     */
    public function test_song_newSku_hasManySongs()
    {
        $album = factory($this->album->class())->create([
            'artist_id' => factory($this->artist->class())->create()->id
        ]);

        $song = factory($this->song->class())->create([
            'track_number' => 1,
            'album_id' => $album->id,
        ]);

        $sku = factory($this->repo->class())->create();

        $song->sku()->associate($sku)->save();

        $this->assertInstanceOf($this->song->class(), $sku->songs->first());
    }
}
