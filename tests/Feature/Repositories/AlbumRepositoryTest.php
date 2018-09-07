<?php

namespace Tests\Feature\Repositories;

use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;

class AlbumRepositoryTest extends RepositoryCrudTestCase
{
    /**
     * @var  $album  AlbumRepositoryInterface
     */
    protected $album;

    /**
     * @var  $artist  ArtistRepositoryInterface
     */
    protected $artist;

    public function setUp()
    {
        parent::setUp();

        #$this->seed('CatalogSeeder');

        $this->artist = resolve(ArtistRepositoryInterface::class);
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
    public function test_method_create_storesNewResource()
    {
        $artist = factory($this->artist->class())->create();

        $album = factory($this->repo->class())->make([
            'artist_id' => $artist->id
        ])->toArray();

        $this->assertInstanceOf(
            $this->repo->class(),
            $this->repo->create($album)
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_updatesResource()
    {
        $artist = factory($this->artist->class())->create();

        $album = factory($this->repo->class())->create([
            'artist_id' => $artist->id
        ]);

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
        $artist = factory($this->artist->class())->create();

        $album = factory($this->repo->class())->create([
            'artist_id' => $artist->id
        ]);

        $updated = $this->repo->update($album->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function test_method_delete_deletesResource()
    {
        $artist = factory($this->artist->class())->create();

        $album = factory($this->repo->class())->create([
            'artist_id' => $artist->id
        ]);

        $album->delete();

        $this->assertNull($this->repo->findById($album->id));
    }
}
