<?php

namespace Tests\Feature\Repositories;

use Illuminate\Support\Str;
use App\Contracts\ProfileRepositoryInterface;
use CountriesSeeder;
use App\Contracts\FlacFileRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;
use App\Contracts\SongRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FlacFileRepositoryTest extends RepositoryCrudTestCase
{
    /**
     * @var $profile ProfileRepositoryInterface
     */
    protected $profile;

    /**
     * @var $artist ArtistRepositoryInterface
     */
    protected $artist;

    /**
     * @var $album AlbumRepositoryInterface
     */
    protected $album;

    /**
     * @var $song SongRepositoryInterface
     */
    protected $song;

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

        $this->song = resolve(SongRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function setRepository()
    {
        $this->repo = resolve(FlacFileRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function testCreateStoresNewResource()
    {
        $flacFile = $this->factory()->make();

        $this->assertInstanceOf(
            $this->repo->class(),
            $this->repo->create($flacFile->toArray())
        );
    }

    /**
     * @inheritdoc
     */
    public function testUpdateUpdatesResource()
    {
        $flacFile = $this->repo->create(
            $this->factory()->raw()
        );

        $newValue = Str::random(32);

        $property = 'md5_data_source';

        $this->repo->update($flacFile->id, [
            $property => $newValue,
        ]);

        $this->assertTrue(
            $this->repo->findById($flacFile->id)->{$property} === $newValue
        );
    }

    /**
     * @inheritdoc
     */
    public function testUpdateReturnsModelInstance()
    {
        $flacFile = $this->repo->create(
            $this->factory()->raw()
        );

        $updated = $this->repo->update($flacFile->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function testDeleteDeletesResource()
    {
        $flacFile = $this->repo->create(
            $this->factory()->raw()
        );

        $flacFile->delete();

        try {
            $this->repo->findById($flacFile->id);
        } catch (ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Ensure that when a FlacFile is related to a Song, the FlacFile has
     * one or more Songs.
     *
     * @return void
     */
    public function testWhenFlacFileRelatedToSongItHasManySongs()
    {
        // TODO This is identical to the AlbumRepositoryTest::makeAlbum() method.
        // Is there any compelling reason to make this more DRY?

        $artist = $this->artist->create(
            $this->factory($this->artist)->raw(
                $this->factory($this->profile)->raw()
            )
        );

        // This is the one property that can't be passed via the argument.

        $properties['artist_id'] = $artist->id;

        $album = $this->factory($this->album)->create(['artist_id' => $artist->id]);

        $flacFile = $this->repo->create(
            $this->factory($this->repo)->raw()
        );

        $album->songs()->first()->flacFile()->associate($flacFile)->save();

        $this->assertInstanceOf(
            $this->song->class(),
            $this->repo->findById($flacFile->id)->songs->first()
        );
    }
}
