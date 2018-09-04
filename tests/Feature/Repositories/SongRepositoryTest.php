<?php

namespace Tests\Feature\Repositories;

use Artisan;
use DB;

use App\Contracts\SongRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;

class SongRepositoryTest extends RepositoryCrudTestCase
{
    public function setUp()
    {
        parent::setUp();

        Artisan::call('db:seed', ['--class' => 'CatalogSeeder']);

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
    public function test_method_update_updatesModel()
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
    public function test_method_delete_deletesModel()
    {
        $album = factory($this->album->class())->create();

        $song = factory($this->repo->class())->create([
            'album_id' => $album->id,
            'track_number' => 1,
        ]);

        DB::transaction(function () use ($song) {
            $song->delete();
        });

        $this->assertNull($this->repo->findById($song->id));
    }
}
