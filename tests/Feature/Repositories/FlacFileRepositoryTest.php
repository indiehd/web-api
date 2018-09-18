<?php

namespace Tests\Feature\Repositories;

use CountriesSeeder;
use App\Contracts\FlacFileRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;
use App\Contracts\SongRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FlacFileRepositoryTest extends RepositoryCrudTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->seed(CountriesSeeder::class);

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
        $flacFile = factory($this->repo->class())->create();

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
        $flacFile = factory($this->repo->class())->create();

        $updated = $this->repo->update($flacFile->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function test_method_delete_deletesResource()
    {
        $flacFile = factory($this->repo->class())->create();

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
        $flacFile = factory($this->repo->class())->create();

        $song = factory($this->song->class())->create([
            'album_id' => factory($this->album->class())->create()->id,
            'track_number' => 1,
        ]);

        $song->flacFile()->associate($flacFile)->save();

        $this->assertInstanceOf(
            $this->song->class(),
            $this->repo->findById($flacFile->id)->songs->first()
        );
    }
}
