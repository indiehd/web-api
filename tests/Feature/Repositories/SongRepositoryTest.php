<?php

namespace Tests\Feature\Repositories;

use App\Contracts\SongRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;

class SongRepositoryTest extends RepositoryCrudTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->seed('CountriesSeeder');

        $this->album = resolve(AlbumRepositoryInterface::class);
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
        $album = factory($this->album->class())->create();

        $song = factory($this->repo->class())->create([
            'album_id' => $album->id,
            'track_number' => 1,
        ]);

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
        $album = factory($this->album->class())->create();

        $song = factory($this->repo->class())->create([
            'album_id' => $album->id,
            'track_number' => 1,
        ]);

        $updated = $this->repo->update($song->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function test_method_delete_deletesResource()
    {
        $album = factory($this->album->class())->create();

        $song = factory($this->repo->class())->create([
            'album_id' => $album->id,
            'track_number' => 1,
        ]);

        $song->delete();

        $this->assertNull($this->repo->findById($song->id));
    }
}
