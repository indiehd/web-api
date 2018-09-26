<?php

namespace Tests\Feature\Repositories;

use App\Contracts\ProfileRepositoryInterface;
use CountriesSeeder;
use App\Contracts\FlacFileRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;
use App\Contracts\SongRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FlacFileRepositoryTest extends RepositoryCrudTestCase
{
    /**
     * @var $profile ProfileRepositoryInterface
     */
    protected $profile;

    /**
     * @var $artist ArtistRepositoryInterface
     */
    protected $artist;

    /**
     * @var $album AlbumRepositoryInterface
     */
    protected $album;

    /**
     * @var $song SongRepositoryInterface
     */
    protected $song;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        $this->seed(CountriesSeeder::class);

        $this->profile = resolve(ProfileRepositoryInterface::class);

        $this->artist = resolve(ArtistRepositoryInterface::class);

        $this->album = resolve(AlbumRepositoryInterface::class);

        $this->song = resolve(SongRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function setRepository()
    {
        $this->repo = resolve(FlacFileRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function test_method_create_storesNewResource()
    {
        $flacFile = factory($this->repo->class())->make();

        $this->assertInstanceOf(
            $this->repo->class(),
            $this->repo->create($flacFile->toArray())
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_updatesResource()
    {
        $flacFile = $this->repo->create(
            factory($this->repo->class())->make()->toArray()
        );

        $newValue = str_random(32);

        $property = 'md5_data_source';

        $this->repo->update($flacFile->id, [
            $property => $newValue,
        ]);

        $this->assertTrue(
            $this->repo->findById($flacFile->id)->{$property} === $newValue
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_returnsModelInstance()
    {
        $flacFile = $this->repo->create(
            factory($this->repo->class())->make()->toArray()
        );

        $updated = $this->repo->update($flacFile->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function test_method_delete_deletesResource()
    {
        $flacFile = $this->repo->create(
            factory($this->repo->class())->make()->toArray()
        );

        $flacFile->delete();

        try {
            $this->repo->findById($flacFile->id);
        } catch(ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Ensure that when a FlacFile is associated with a Song, the FlacFile has
     * one or more Songs.
     *
     * @return void
     */
    public function test_song_flacFile_hasManySongs()
    {
        // TODO This is identical to the AlbumRepositoryTest::makeAlbum() method.
        // Is there any compelling reason to make this more DRY?

        $artist = $this->artist->create(
            factory($this->artist->class())->make(
                factory($this->profile->class())->make()->toArray()
            )->toArray()
        );

        // This is the one property that can't passed via the argument.

        $properties['artist_id'] = $artist->id;

        $album = $this->album->create(
            factory($this->album->class())->make(['artist_id' => $artist->id])->toArray()
        );

        $song = factory($this->song->class())->create([
            'album_id' => $album->id,
            'track_number' => 1,
        ]);

        $flacFile = $this->repo->create(
            factory($this->repo->class())->make()->toArray()
        );

        $song->flacFile()->associate($flacFile)->save();

        $this->assertInstanceOf(
            $this->song->class(),
            $this->repo->findById($flacFile->id)->songs->first()
        );
    }
}
