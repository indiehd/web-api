<?php

namespace Tests\Feature\Repositories;

use CountriesSeeder;
use App\Contracts\GenreRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

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
        $genre = $this->factory()->make();

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
            $this->factory()->raw()
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
            $this->factory()->raw()
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
            $this->factory()->raw()
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
        $album = $this->factory($this->album)->create($this->makeAlbum()->toArray());

        $genre = $this->repo->create(
            $this->factory()->raw()
        );

        $album->genres()->attach($genre->id);

        $this->assertInstanceOf($this->album->class(), $genre->albums->first());
    }

    /**
     * Ensure that attempts to create a Genre whose name is identical to an
     * existing Genre cause an exception to be thrown.
     *
     * @return void
     */
    public function testWhenGenreNameAlreadyExistsExceptionIsThrown()
    {
        $this->repo->create(
            $this->factory()->raw(['name' => 'Foo'])
        );

        try {
            $this->repo->create(
                $this->factory()->raw(['name' => 'Foo'])
            );
        } catch (QueryException $e) {
            $this->assertEquals($e->getCode(), '23000');
        }
    }

    /**
     * Ensure that the `approved_at` and `approver_id` fields are null upon
     * initial creation.
     *
     * @return void
     */
    public function testWhenGenreIsCreatedApprovalFieldsAreNull()
    {
        $genre = $this->repo->create(
            $this->factory()->raw(['name' => 'Foo'])
        );

        $this->assertNull($genre->approved_at);
        $this->assertNull($genre->approver);
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
            $this->factory($this->artist)->make(
                $this->factory($this->profile)->raw()
            )->toArray()
        );

        // This is the one property that can't be passed via the argument.

        $properties['artist_id'] = $artist->id;

        return $this->factory($this->album)->make($properties);
    }
}
