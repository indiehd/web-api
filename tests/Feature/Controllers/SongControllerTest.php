<?php

namespace Tests\Feature\Controllers;

use App\Contracts\AlbumRepositoryInterface;
use CountriesSeeder;
use App\Song;
use App\FlacFile;
use App\Contracts\SongRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\CatalogEntityRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
#use App\Http\Resources\ArtistResource;
#use App\Http\Resources\AlbumResource;

class SongControllerTest extends ControllerTestCase
{
    protected $song;

    protected $artist;

    protected $album;

    protected $user;

    protected $profile;

    protected $catalogEntity;

    /**
     * @inheritdoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seed(CountriesSeeder::class);

        $this->song = resolve(SongRepositoryInterface::class);

        $this->artist = resolve(ArtistRepositoryInterface::class);

        $this->album = resolve(AlbumRepositoryInterface::class);

        $this->user = resolve(UserRepositoryInterface::class);

        $this->profile = resolve(ProfileRepositoryInterface::class);

        $this->catalogEntity = resolve(CatalogEntityRepositoryInterface::class);
    }

    protected function getExactJson(Song $model)
    {
        return [
            'id' => $model->id,
            'name' => $model->name,
            'alt_name' => $model->alt_name,
            'flac_file' => $model->flac_file,
            'preview_start' => number_format($model->preview_start, 3),
            'track_number' => $model->track_number,
            'is_active' => (int) $model->is_active,
            'deleted_at' => null,
        ];
    }

    protected function getExactJsonWithFlacFile(FlacFile $model)
    {
        return [
            'id' => $model->id,
            'file_size' => $model->file_size,
        ];
    }

    protected function getJsonStructureForLabel()
    {
        return [
            'id',
            'artists_count',
            'albums_count',
        ];
    }

    /**
     * Ensure that a request for the index returns OK HTTP status and the
     * expected JSON string.
     */
    public function testAllReturnsOkStatusAndExpectedJsonStructure()
    {
        $album = factory($this->album->class())->create();

        $song = factory($this->song->class())->create([
            'track_number' => 1,
            'album_id' => $album->id,
        ]);

        $this->json('GET', route('songs.index'))
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [$this->getExactJson($song)]
            ]);
    }

    /**
     * Ensure that a request for an existing record returns OK HTTP status and
     * the expected JSON string.
     */
    public function testShowReturnsOkStatusAndExpectedJsonStructure()
    {
        $album = factory($this->album->class())->create();

        $song = factory($this->song->class())->create([
            'track_number' => 1,
            'album_id' => $album->id,
        ]);

        $this->json('GET', route('songs.show', ['id' => $song->id]))
            ->assertStatus(200)
            ->assertExactJson([
                'data' => $this->getExactJson($song)
            ]);
    }

    /**
     * Ensure that a request for an existing record returns OK HTTP status and
     * the expected JSON string.
     */
    public function testShowWhenSongBelongsToFlacFileReturnsOkStatusAndExpectedJsonStructure()
    {
        $album = factory($this->album->class())->create();

        $song = factory($this->song->class())->create([
            'track_number' => 1,
            'album_id' => $album->id,
        ]);

        $this->json('GET', route('songs.show', ['id' => $song->id]))
            ->assertStatus(200)
            ->assertExactJson([
                'data' => $this->getExactJsonWithFlacFile($song->flacFile)
            ]);
    }
}
