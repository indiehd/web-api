<?php

namespace Tests\Feature\Controllers;

use CountriesSeeder;

use App\Album;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\UserRepositoryInterface;

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

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(CountriesSeeder::class);

        $this->artist = resolve(ArtistRepositoryInterface::class);
        $this->profile = resolve(ProfileRepositoryInterface::class);
        $this->user = resolve(UserRepositoryInterface::class);

        $this->album = resolve(AlbumRepositoryInterface::class);
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

    protected function getJsonStructure(Album $album, $castIntAndBool = false)
    {
        $structure = [
            'title' => $album->title,
            'alt_title' => $album->alt_title,
            'year' => $album->year,
            'description' => $album->description,
            'has_explicit_lyrics' => $album->has_explicit_lyrics,
            'full_album_price' => $album->full_album_price,
            'rank' => $album->rank,
            'is_active' => $album->is_active,
            'deleter' => $album->deleter,
            'deleted_at' => null,
        ];

        if ($castIntAndBool) {
            $structure['year'] = (int) $album->year;
            $structure['has_explicit_lyrics'] = (int) $album->has_explicit_lyrics;
            $structure['is_active'] = (int) $album->is_active;
        }

        if (isset($album->id)) {
            $structure['id'] = $album->id;
        }

        return $structure;
    }

    protected function getAllInputsInValidState()
    {
        return [
            'title' => 'Foobar\'s Fiddle-Along',
            'year' => (int) 1981,
            'description' => 'The best album, evaaah!',
            'has_explicit_lyrics' => 0,
            'full_album_price' => 9.99,
        ];
    }

    public function testAllReturnsOkStatusAndExpectedJsonStructure()
    {
        $album = $this->createAlbum();

        $this->json('GET', route('albums.index'))
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    $this->getJsonStructure($album, true)
                ]
            ]);
    }

    public function testShowReturnsOkStatusAndExpectedJsonStructure()
    {
        $album = $this->createAlbum();

        $this->json('GET', route('albums.show', ['id' => $album->id]))
            ->assertStatus(200)
            ->assertExactJson([
                'data' => $this->getJsonStructure($album, true)
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

        $album = factory($this->album->class())->state('withSongs')->make();

        $albumAsArray = $album->toArray();

        $this->actingAs($artist->user)
            ->json('POST', route('albums.store'), $albumAsArray)
            ->assertStatus(201)
            ->assertJson([
                'data' => $this->getJsonStructure($album)
            ]);
    }

    public function testStoreWithInvalidInputReturnsUnprocessableEntityStatusAndExpectedJsonStructure()
    {
        $artist = factory($this->artist->class())->create();

        $this->actingAs($artist->user)
            ->json('POST', route('albums.store'), [])
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors'
            ]);
    }

    public function testUpdateWhenNotAuthenticatedResultsInNotAuthorizedResponse()
    {
        $album = $this->createAlbum();

        $this->json(
            'PUT', route('albums.update', ['id' => $album->id]),
            $this->getAllInputsInValidState()
        )
            ->assertStatus(403);
    }

    public function testUpdateWithValidInputReturnsOkStatusAndExpectedJsonStructure()
    {
        $album = $this->createAlbum();

        $album->title = 'Some New Title';

        $albumAsArray = $album->toArray();

        $this->actingAs($album->artist->user)
            ->json('PUT', route('albums.update', ['id' => $album->id]), $albumAsArray)
            ->assertStatus(200)
            ->assertExactJson([
                'data' => $this->getJsonStructure($album, true)
            ]);
    }

    public function testUpdateWithInvalidInputReturnsUnprocessableEntityStatusAndExpectedJsonStructure()
    {
        $album = $this->createAlbum();

        $this->actingAs($album->artist->user)
            ->json('PUT', route('albums.update', ['id' => $album->id]), ['title' => ''])
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

        $this->actingAs($album->artist->user)
            ->json('DELETE', route('albums.destroy', ['id' => $album->id]))
            ->assertStatus(200);
    }

    public function testDestroyWithInvalidInputReturnsUnprocessableEntityStatus()
    {
        $album = $this->createAlbum();

        $this->actingAs($album->artist->user)
            ->json('DELETE', route('albums.destroy', ['id' => 'foo']))
            ->assertStatus(404);
    }

    public function testDestroyWithMissingInputReturnsMethodNotAllowedStatus()
    {
        $this->json('DELETE', route('albums.destroy', ['id' => null]))
            ->assertStatus(405);
    }
}
