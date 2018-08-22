<?php

namespace Tests\Feature\Repositories;

use App\Contracts\ArtistRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Contracts\AlbumRepositoryInterface;

class AlbumRepositoryTest extends RepositoryTestCase
{
    use RefreshDatabase;

    /**
     * @var $album AlbumRepositoryInterface
     */
    protected $album;

    /**
     * @var $artist ArtistRepositoryInterface
     */
    protected $artist;

    public function setUp()
    {
        parent::setUp();

        $this->artist = resolve(ArtistRepositoryInterface::class);
    }

    /**
     * Sets the $repo property.
     *
     * @return void
     */
    public function setRepository()
    {
        $this->repo = resolve(AlbumRepositoryInterface::class);
    }

    /**
     * Ensure the method create() creates a new record in the database.
     *
     * @return void
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
     * Ensure that the update() method updates the model record in the database.
     *
     * @return void
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
     * Ensure that the delete() method results in model deletion.
     *
     * return @void
     */
    public function test_method_delete_deletesAlbum()
    {
        $artist = $this->artist->model()->inRandomOrder()->first();

        $album = factory($this->repo->class())->create([
            'artist_id' => $artist->id
        ]);

        $album->delete();

        $this->assertNull($this->repo->findById($album->id));
    }
}
