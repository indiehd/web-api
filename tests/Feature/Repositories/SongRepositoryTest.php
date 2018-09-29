<?php

namespace Tests\Feature\Repositories;

use App\Contracts\SongRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;
use App\Contracts\FlacFileRepositoryInterface;
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
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        $this->seed('CountriesSeeder');

        $this->album = resolve(AlbumRepositoryInterface::class);

        $this->flacFile = resolve(FlacFileRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function setRepository()
    {
        $this->repo = resolve(SongRepositoryInterface::class);
    }

    /**
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
}
