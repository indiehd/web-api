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
    public function setUp(): void
    {
        parent::setUp();

        $this->seed(CountriesSeeder::class);

        $this->profile = resolve(ProfileRepositoryInterface::class);

        $this->artist = resolve(ArtistRepositoryInterface::class);

        $this->album = resolve(AlbumRepositoryInterface::class);
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
    public function testCreateStoresNewResource()
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
    public function testUpdateUpdatesResource()
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
    public function testUpdateReturnsModelInstance()
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
    public function testDeleteDeletesResource()
    {
        $genre = $this->repo->create(
            factory($this->repo->class())->raw()
        );

        $genre->delete();

        try {
            $this->repo->findById($genre->id);
        } catch (ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Ensure that when a Genre is related to an Album, the Genre has
     * one or more Albums.
     *
     * @return void
     */
    public function testWhenGenreAssociatedWithAlbumItHasManyAlbums()
    {
        $album = factory($this->album->class())->create($this->makeAlbum()->toArray());

        $genre = $this->repo->create(
            factory($this->repo->class())->raw()
        );

        $album->genres()->attach($genre->id);

        $this->assertInstanceOf($this->album->class(), $genre->albums->first());
    }

    /**
     * Make an Album.
     *
     * @param array $properties
     * @return \App\Album
     */
    protected function makeAlbum(array $properties = [])
    {
        $artist = $this->artist->create(
            factory($this->artist->class())->make(
                factory($this->profile->class())->raw()
            )->toArray()
        );

        // This is the one property that can't be passed via the argument.

        $properties['artist_id'] = $artist->id;

        return factory($this->album->class())->make($properties);
    }
}
