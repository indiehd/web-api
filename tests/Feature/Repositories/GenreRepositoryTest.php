<?php

namespace Tests\Feature\Repositories;

use CountriesSeeder;
use App\Contracts\GenreRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GenreRepositoryTest extends RepositoryCrudTestCase
{
    /**
     * @var ProfileRepositoryInterface $profile
     */
    protected $profile;

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

        $this->profile = resolve(ProfileRepositoryInterface::class);

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
     * Creates a new Album.
     *
     * @param array $properties
     * @return \App\Album
     */
    public function makeAlbum(array $properties = [])
    {
        $artist = $this->artist->create(
            factory($this->artist->class())->make(
                factory($this->profile->class())->raw()
            )->toArray()
        );

        // This is the one property that can't passed via the argument.

        $properties['artist_id'] = $artist->id;

        return factory($this->album->class())->make($properties);
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
        $genre = $this->repo->create(
            factory($this->repo->class())->raw()
        );

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
        $genre = $this->repo->create(
            factory($this->repo->class())->raw()
        );

        $updated = $this->repo->update($genre->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function test_method_delete_deletesResource()
    {
        $genre = $this->repo->create(
            factory($this->repo->class())->raw()
        );

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
        $album = $this->album->create($this->makeAlbum()->toArray());

        $genre = $this->repo->create(
            factory($this->repo->class())->raw()
        );

        $album->genres()->attach($genre->id);

        $this->assertInstanceOf($this->album->class(), $genre->albums->first());
    }
}
