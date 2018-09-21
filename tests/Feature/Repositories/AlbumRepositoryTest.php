<?php

namespace Tests\Feature\Repositories;

use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;
use App\Contracts\GenreRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\SongRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AlbumRepositoryTest extends RepositoryCrudTestCase
{
    /**
     * @var $profile ProfileRepositoryInterface
     */
    protected $profile;

    /**
     * @var $album AlbumRepositoryInterface
     */
    protected $album;

    /**
     * @var $artist ArtistRepositoryInterface
     */
    protected $artist;

    /**
     * @var $song SongRepositoryInterface
     */
    protected $song;

    /**
     * @var $genre GenreRepositoryInterface
     */
    protected $genre;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        $this->seed('CountriesSeeder');

        $this->profile = resolve(ProfileRepositoryInterface::class);

        $this->artist = resolve(ArtistRepositoryInterface::class);

        $this->song = resolve(SongRepositoryInterface::class);

        $this->genre = resolve(GenreRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function setRepository()
    {
        $this->repo = resolve(AlbumRepositoryInterface::class);
    }

    /**
     * Make a new Album object.
     *
     * @param array $properties
     * @return \App\Album
     *
     */
    public function make(array $properties = [])
    {
        return factory($this->repo->class())->make($properties);
    }

    /**
     * @inheritdoc
     */
    public function test_method_create_storesNewResource()
    {
        $artist = $this->artist->testClass()->make(
            $this->profile->testClass()->make()->toArray()
        );

        $artist = $this->artist->create($artist->toArray());

        $album = $this->make(['artist_id' => $artist->id]);

        $this->assertInstanceOf(
            $this->repo->class(),
            $this->repo->create($album->toArray())
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

        try {
            $this->repo->findById($album->id);
        } catch(ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Ensure that a newly-created Album belongs to an Artist.
     *
     * @return void
     */
    public function test_artist_albumBelongsToArtist()
    {
        $this->assertInstanceOf($this->artist->class(), $this->make()->artist);
    }

    /**
     * Ensure that a newly-created Album has one or more Songs.
     *
     * @return void
     */
    public function test_songs_albumHasManySongs()
    {
        $album = factory($this->repo->class())->create([
            'artist_id' => factory($this->artist->class())->create()->id
        ]);

        factory($this->song->class())->create([
            'track_number' => 1,
            'album_id' => $album->id
        ]);

        $this->assertInstanceOf($this->song->class(), $album->songs->first());
    }

    /**
     * Ensure that a newly-created Album belongs to one or more Genres.
     *
     * @return void
     */
    public function test_genres_albumBelongsToManyGenres()
    {
        $album = factory($this->repo->class())->create([
            'artist_id' => factory($this->artist->class())->create()->id
        ]);

        $album->genres()->attach(factory($this->genre->class())->create()->id);

        $this->assertInstanceOf($this->genre->class(), $album->genres->first());
    }
}
