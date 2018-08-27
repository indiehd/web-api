<?php

namespace Tests\Feature\Repositories;

use DB;

use App\Contracts\SongRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;

class SongRepositoryTest extends RepositoryCrudTestCase
{
    public function setUp()
    {
        parent::setUp();

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
    public function test_method_create_storesNewModel()
    {
        $song = factory($this->repo->class())->make()->toArray();

        $this->assertInstanceOf(
            $this->repo->class(),
            $this->repo->create($song)
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_updatesModel()
    {
        $album = factory($this->album->class())->create();

        $song = factory($this->repo->class())->create(['album_id' => $album->id]);

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
    public function test_method_delete_deletesModel()
    {
        $album = factory($this->album->class())->create();

        $song = factory($this->repo->class())->create(['album_id' => $album->id]);

        DB::transaction(function () use ($song) {
            $song->delete();
        });

        $this->assertNull($this->repo->findById($song->id));
    }
}
