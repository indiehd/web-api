<?php

namespace Tests\Feature\Controllers;

use App\Contracts\AlbumRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\CatalogEntityRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\SongRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\FlacFile;
use App\Song;
use CountriesSeeder;

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

    protected function getExactJson(Song $model): array
    {
        return [
            'id' => $model->id,
            'name' => $model->name,
            'alt_name' => $model->alt_name,
            'track_number' => $model->track_number,
            'preview_start' => $model->preview_start,
            'is_active' => $model->is_active,
            'deleted_at' => null,
        ];
    }

    protected function getExactJsonWithFlacFile(FlacFile $model): array
    {
        return [
            'id' => $model->id,
            'file_size' => $model->file_size,
        ];
    }

    protected function getJsonStructureForLabel(): array
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
        $album = factory($this->album->class())->create(['is_active' => 1]);

        $this->json('GET', route('songs.index'))
            ->assertStatus(200)
            ->assertJson(['data' => [
                $this->getExactJson($album->songs->first())
            ]]);
    }

    /**
     * Ensure that a request for an existing record returns OK HTTP status and
     * the expected JSON string.
     */
    public function testShowReturnsOkStatusAndExpectedJsonStructure()
    {
        $album = factory($this->album->class())->create(['is_active' => 1]);

        $this->json('GET', route('songs.show', ['id' => $album->songs->first()->id]))
            ->assertStatus(200)
            ->assertJson(['data' =>
                $this->getExactJson($album->songs->first())
            ]);
    }

    /**
     * Ensure that a request for an existing record with the specified
     * relationship returns OK HTTP status and the expected JSON string.
     */
    public function testShowWhenSongBelongsToFlacFileReturnsOkStatusAndExpectedJsonStructure()
    {
        $album = factory($this->album->class())->create(['is_active' => 1]);

        $this->json('GET', route('songs.show', ['id' => $album->songs->first()->id]))
            ->assertStatus(200)
            ->assertJson([
                'data' => ['flac_file' => $this->getExactJsonWithFlacFile(
                    $album->songs->first()->flacFile
                )
            ]]);
    }
}
