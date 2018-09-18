<?php

namespace Tests\Feature\Repositories;

use CountriesSeeder;
use App\Contracts\GenreRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GenreRepositoryTest extends RepositoryCrudTestCase
{
    /**
     * @var ArtistRepositoryInterface $artist
     */
    protected $artist;

    /**
     * @var AlbumRepositoryInterface $album
     */
    protected $album;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        $this->seed(CountriesSeeder::class);

        $this->album = resolve(AlbumRepositoryInterface::class);

        $this->artist = resolve(ArtistRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function setRepository()
    {
        $this->repo = resolve(GenreRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function test_method_create_storesNewResource()
    {
        $genre = factory($this->repo->class())->make();

        $this->assertInstanceOf(
            $this->repo->class(),
            $this->repo->create($genre->toArray())
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_updatesResource()
    {
        $genre = factory($this->repo->class())->create();

        $newValue = 'Some New Genre';

        $property = 'name';

        $this->repo->update($genre->id, [
            $property => $newValue,
        ]);

        $this->assertTrue(
            $this->repo->findById($genre->id)->{$property} === $newValue
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_returnsModelInstance()
    {
        $genre = factory($this->repo->class())->create();

        $updated = $this->repo->update($genre->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function test_method_delete_deletesResource()
    {
        $genre = factory($this->repo->class())->create();

        $genre->delete();

        try {
            $this->repo->findById($genre->id);
        } catch(ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Ensure that when a Genre is associated with an Album, the Genre has
     * one or more Albums.
     *
     * @return void
     */
    public function test_albums_randomAlbum_belongsToManyGenres()
    {
        $artist = factory($this->artist->class())->create();

        $album = factory($this->album->class())->create(['artist_id' => $artist->id]);

        $genre = factory($this->repo->class())->create();

        $album->genres()->attach($genre->id);

        $this->assertInstanceOf($this->album->class(), $genre->albums->first());
    }
}
