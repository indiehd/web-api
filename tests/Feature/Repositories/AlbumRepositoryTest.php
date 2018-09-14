<?php

namespace Tests\Feature\Repositories;

use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;
use App\Contracts\GenreRepositoryInterface;
use App\Contracts\SongRepositoryInterface;

class AlbumRepositoryTest extends RepositoryCrudTestCase
{
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

    public function setUp()
    {
        parent::setUp();

        $this->seed('CountriesSeeder');

        $this->seed('GenresSeeder');

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
     * Creates a new Album.
     *
     * @return \App\Album
     */
    public function createAlbum()
    {
        return factory($this->repo->class())->create([
            'artist_id' => factory($this->artist->class())->create()->id
        ]);
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

    /**
     * Ensure that a newly-created Album belongs to an Artist.
     *
     * @return void
     */
    public function test_artist_albumBelongsToArtist()
    {
        $this->assertInstanceOf($this->artist->class(), $this->createAlbum()->artist);
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

        $album->genres()->attach(1);

        $this->assertInstanceOf($this->genre->class(), $album->genres->first());
    }
}
