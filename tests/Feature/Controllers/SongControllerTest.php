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
use IndieHD\Velkart\Database\Seeders\CountriesSeeder;

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
        $album = $this->factory($this->album)->create(['is_active' => 1]);

        $this->json('GET', route('songs.index'))
            ->assertStatus(200)
            ->assertJson(['data' => [
                $this->getExactJson($album->songs->first()),
            ]]);
    }

    /**
     * Ensure that Songs on inactive Albums are excluded, regardless of the
     * Songs' individual statuses.
     */
    public function testAllExcludesSongsOnInactiveAlbums()
    {
        $this->factory($this->album)->create(['is_active' => 0]);

        $this->json('GET', route('songs.index'))
            ->assertStatus(200)
            ->assertExactJson(['data' => []]);
    }

    /**
     * Ensure that inactive Songs are excluded (regardless of the Album's status).
     */
    public function testAllExcludesInactiveSongs()
    {
        $album = $this->factory($this->album)->create(['is_active' => 1]);

        $song = $album->songs->first();

        $song->is_active = 0;

        $song->save();

        $r = $this->json('GET', route('songs.index'));

        $songIds = collect(json_decode($r->getContent(), true)['data'])->pluck('id')->toArray();

        $this->assertFalse(in_array($album->songs->first()->id, $songIds));
    }

    /**
     * Ensure that a request for an existing record returns OK HTTP status and
     * the expected JSON string.
     */
    public function testShowReturnsOkStatusAndExpectedJsonStructure()
    {
        $album = $this->factory($this->album)->create(['is_active' => 1]);

        $this->json('GET', route('songs.show', ['id' => $album->songs->first()->id]))
            ->assertStatus(200)
            ->assertJson(['data' => $this->getExactJson($album->songs->first())]);
    }

    /**
     * Ensure that a request for an existing record with the specified
     * relationship returns OK HTTP status and the expected JSON string.
     */
    public function testShowWhenSongBelongsToFlacFileReturnsOkStatusAndExpectedJsonStructure()
    {
        $album = $this->factory($this->album)->create(['is_active' => 1]);

        $this->json('GET', route('songs.show', ['id' => $album->songs->first()->id]))
            ->assertStatus(200)
            ->assertJson([
                'data' => ['flac_file' => $this->getExactJsonWithFlacFile(
                    $album->songs->first()->flacFile
                )],
            ]);
    }

    /**
     * Ensure that when a Song is inactive and a User is not logged-in,
     * access is denied.
     */
    public function testShowWhenSongIsInactiveAndNoUserReturnsAccessDenied()
    {
        $album = $this->factory($this->album)->create(['is_active' => 1]);

        $song = $album->songs->first();

        $song->is_active = 0;

        $song->save();

        $this->json('GET', route('songs.show', ['id' => $album->songs->first()->id]))
            ->assertStatus(403);
    }

    /**
     * Ensure that when a Song is inactive and a User is logged-in, but the User
     * doesn't own the Song, access is denied.
     */
    public function testShowWhenSongIsInactiveAndUserNotOwnerReturnsAccessDenied()
    {
        $album = $this->factory($this->album)->create(['is_active' => 1]);

        $song = $album->songs->first();

        $song->is_active = 0;

        $song->save();

        $this->actingAs($this->factory($this->user)->create())
            ->json('GET', route('songs.show', ['id' => $album->songs->first()->id]))
            ->assertStatus(403);
    }

    /**
     * Ensure that when a Song is inactive, but the User owns the Song,
     * access is allowed.
     */
    public function testShowWhenSongIsInactiveOwnerCanStillView()
    {
        $album = $this->factory($this->album)->create();

        $song = $album->songs->first();

        $song->is_active = 0;

        $song->save();

        $this->actingAs($album->artist->user)
            ->json('GET', route('songs.show', ['id' => $album->songs->first()->id]))
            ->assertStatus(200);
    }

    /**
     * Ensure that when a Song is active, but its parent Album is inactive,
     * and a User is not logged-in, access is denied. (The logic here is that an
     * Album's active/inactive status "overrides" the individual Song's status
     * for non-owners; the owner has access regardless of both the Album and
     * Song's statuses.).
     */
    public function testShowWhenSongIsActiveButAlbumIsInactiveAndNoUserReturnsAccessDenied()
    {
        $album = $this->factory($this->album)->create(['is_active' => 0]);

        $song = $album->songs->first();

        $song->is_active = 1;

        $song->save();

        $this->json('GET', route('songs.show', ['id' => $album->songs->first()->id]))
            ->assertStatus(403);
    }
}
