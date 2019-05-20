<?php

namespace Tests\Feature\Controllers;

use CountriesSeeder;

use App\Album;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Http\Requests\StoreArtist;
use App\Http\Requests\UpdateArtist;

class AlbumControllerTest extends ControllerTestCase
{
    /**
     * @var AlbumRepositoryInterface $album
     */
    protected $album;

    /**
     * @var ArtistRepositoryInterface $artist
     */
    protected $artist;

    /**
     * @var ProfileRepositoryInterface $profile
     */
    protected $profile;

    /**
     * @var UserRepositoryInterface $user
     */
    protected $user;

    /**
     * @var StoreAlbum
     */
    protected $storeArtist;

    /**
     * @var UpdateAlbum
     */
    protected $updateArtist;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(CountriesSeeder::class);

        $this->artist = resolve(ArtistRepositoryInterface::class);
        $this->profile = resolve(ProfileRepositoryInterface::class);
        $this->user = resolve(UserRepositoryInterface::class);

        $this->album = resolve(AlbumRepositoryInterface::class);

        $this->storeAlbum = new StoreAlbum();
        $this->updateAlbum = new UpdateAlbum();
    }

    protected function createArtist()
    {
        $artist = factory($this->artist->class())->create();

        factory($this->profile->class())->create(
            [
                'profilable_id' => $artist->id,
                'profilable_type' => $this->artist->class()
            ]
        );

        return $artist;
    }

    protected function createAlbum()
    {
        return factory($this->album->class())->create([
            'is_active' => true,
            'has_explicit_lyrics' => false,
        ]);
    }

    protected function getJsonStructure(Album $album)
    {
        return [
            'id' => $album->id,
            'title' => $album->title,
            'alt_title' => $album->alt_title,
            'year' => (int) $album->year,
            'description' => $album->description,
            'has_explicit_lyrics' => (int) $album->has_explicit_lyrics,
            'full_album_price' => $album->full_album_price,
            'rank' => $album->rank,
            'is_active' => (int) $album->is_active,
            'deleter' => $album->deleter,
            'deleted_at' => null,
        ];
    }

    // TODO Move this into ArtistControllerTest
    /*
    protected function getJsonStructureForOne(Album $album)
    {
        return [
            'albums' => [
                [
                    'id' => $album->id,
                    'title' => $album->title,
                    'alt_title' => $album->alt_title,
                    'year' => (int) $album->year,
                    'description' => $album->description,
                    'has_explicit_lyrics' => (int) $album->has_explicit_lyrics,
                    'full_album_price' => $album->full_album_price,
                    'rank' => $album->rank,
                    'is_active' => (int) $album->is_active,
                    'deleter' => $album->deleter,
                    'deleted_at' => null,
                ],
            ],
            'id' => $album->id,
            'label' => null,
            'profile' => null,
            'songs' => [],
        ];
    }
    */

    protected function getAllInputsInValidState()
    {
        return [
            'title' => 'Foobius Barius',
        ];
    }

    public function testAllReturnsOkStatusAndExpectedJsonStructure()
    {
        $album = $this->createAlbum();

        $this->json('GET', route('albums.index'))
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    $this->getJsonStructure($album)
                ]
            ]);
    }

    public function testShowReturnsOkStatusAndExpectedJsonStructure()
    {
        $album = $this->createAlbum();

        $this->json('GET', route('albums.show', ['id' => $album->id]))
            ->assertStatus(200)
            ->assertExactJson([
                'data' => $this->getJsonStructure($album)
            ]);
    }

    public function testStoreWhenNotAuthenticatedResultsInNotAuthorizedResponse()
    {
        $this->json('POST', route('albums.store'), $this->getAllInputsInValidState())
            ->assertStatus(403);
    }

    public function testStoreWithValidInputReturnsOkStatusAndExpectedJsonStructure()
    {
        $artist = factory($this->artist->class())->create();

        $this->actingAs($artist->user)
            ->json('POST', route('albums.store'), $this->getAllInputsInValidState())
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => $this->getJsonStructure()
            ]);
    }

    public function testStoreWithInvalidInputReturnsUnprocessableEntityStatusAndExpectedJsonStructure()
    {
        $this->json('POST', route('albums.store'), [])
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors'
            ]);
    }

    public function testUpdateWhenNotAuthenticatedResultsInNotAuthorizedResponse()
    {
        $album = $this->createAlbum();

        $this->json('POST', route('albums.update', ['id' => $album->id]), $this->getAllInputsInValidState())
            ->assertStatus(403);
    }

    public function testUpdateWithValidInputReturnsOkStatusAndExpectedJsonStructure()
    {
        $album = $this->createAlbum();

        $inputs = $this->getAllInputsInValidState();

        $inputs['title'] = 'Some New Title';

        $this->json('PUT', route('albums.update', ['id' => $album->id]), $inputs)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->getJsonStructure()
            ]);
    }

    public function testUpdateWithInvalidInputReturnsUnprocessableEntityStatusAndExpectedJsonStructure()
    {
        $album = $this->createAlbum();

        $this->json('PUT', route('albums.update', ['id' => $album->id]), ['title' => ''])
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors'
            ]);
    }

    public function testDestroyWhenNotAuthenticatedResultsInNotAuthorizedResponse()
    {
        $album = $this->createAlbum();

        $this->json('DELETE', route('albums.destroy', ['id' => $album->id]))
            ->assertStatus(403);
    }

    public function testDestroyWithValidInputReturnsOkStatusAndExpectedJsonStructure()
    {
        $album = $this->createAlbum();

        $this->json('DELETE', route('albums.destroy', ['id' => $album->id]))
            ->assertStatus(200)
            ->assertJsonStructure([]);
    }

    public function testDestroyWithInvalidInputReturnsUnprocessableEntityStatus()
    {
        $this->json('DELETE', route('albums.destroy', ['id' => 'foo']))
            ->assertStatus(404);
    }

    public function testDestroyWithMissingInputReturnsMethodNotAllowedStatus()
    {
        $this->json('DELETE', route('albums.destroy', ['id' => null]))
            ->assertStatus(405);
    }
}
