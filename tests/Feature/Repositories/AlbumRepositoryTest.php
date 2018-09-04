<?php

namespace Tests\Feature\Repositories;

use Artisan;
use DB;

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

        Artisan::call('db:seed', ['--class' => 'CatalogSeeder']);

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
    public function test_method_create_storesNewModel()
    {
        $album = factory($this->repo->class())->make([
            'artist_id' => $this->artist->model()->inRandomOrder()->first()->id
        ])->toArray();

        $this->assertInstanceOf(
            $this->repo->class(),
            $this->repo->create($album)
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_updatesModel()
    {
        $artist = $this->artist->model()->inRandomOrder()->first();

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
    public function test_method_delete_deletesModel()
    {
        $artist = $this->artist->model()->inRandomOrder()->first();

        $album = factory($this->repo->class())->create([
            'artist_id' => $artist->id
        ]);

        $album->delete();

        $this->assertNull($this->repo->findById($album->id));
    }
}
