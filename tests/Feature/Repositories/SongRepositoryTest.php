<?php

namespace Tests\Feature\Repositories;

use App\Contracts\SongRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;
use App\Contracts\FlacFileRepositoryInterface;
use App\Contracts\SkuRepositoryInterface;
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
     * @var SkuRepositoryInterface $sku
     */
    protected $sku;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        $this->seed('CountriesSeeder');

        $this->album = resolve(AlbumRepositoryInterface::class);

        $this->flacFile = resolve(FlacFileRepositoryInterface::class);

        $this->sku = resolve(SkuRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function setRepository()
    {
        $this->repo = resolve(SongRepositoryInterface::class);
    }

    public function spawnSong()
    {
        return factory($this->repo->class())->create([
            'album_id' => factory($this->album->class())->create()->id,
            'track_number' => 1,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function test_method_create_storesNewResource()
    {
        $album = factory($this->album->class())->create();

        $song = factory($this->repo->class())->make([
            'album_id' => $album->id,
            'track_number' => 1,
        ])->toArray();

        $this->assertInstanceOf(
            $this->repo->class(),
            $this->repo->create($song)
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_updatesResource()
    {
        $song = $this->spawnSong();

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
        $song = $this->spawnSong();

        $updated = $this->repo->update($song->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function test_method_delete_deletesResource()
    {
        $song = $this->spawnSong();

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
        $this->assertInstanceOf($this->album->class(), $this->spawnSong()->album);
    }

    /**
     * Ensure that when a Song is associated with a FlacFile, the Song belongs
     * to a FlacFile.
     *
     * @return void
     */
    public function test_flacFile_song_belongsToFlacFile()
    {
        $song = factory($this->repo->class())->create([
            'album_id' => factory($this->album->class())->create()->id,
            'track_number' => 1,
        ]);

        $this->assertInstanceOf($this->flacFile->class(), $this->spawnSong()->flacFile);
    }

    /**
     * Ensure that a newly-created Song belongs to a Sku.
     *
     * @return void
     */
    public function test_sku_song_belongsToSku()
    {
        $this->assertInstanceOf($this->sku->class(), $this->spawnSong()->sku);
    }
}
