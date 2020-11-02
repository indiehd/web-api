<?php

namespace Tests\Feature\Controllers;

use App\Album;
use App\Contracts\AlbumRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Http\Resources\AlbumResource;
use CountriesSeeder;
use Illuminate\Support\Arr;
use Money\Money;

class AlbumControllerTest extends ControllerTestCase
{
    /**
     * @var AlbumRepositoryInterface
     */
    protected $album;

    /**
     * @var ArtistRepositoryInterface
     */
    protected $artist;

    /**
     * @var ProfileRepositoryInterface
     */
    protected $profile;

    /**
     * @var UserRepositoryInterface
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
        $artist = $this->factory($this->artist)->create();

        $this->factory($this->profile)->create(
            [
                'profilable_id' => $artist->id,
                'profilable_type' => $this->artist->class(),
            ]
        );

        return $artist;
    }

    protected function createAlbum()
    {
        return $this->factory($this->album)->create([
            'is_active' => true,
            'has_explicit_lyrics' => false,
        ]);
    }

    protected function getJsonStructure(Album $album)
    {
        return AlbumResource::make($album)->jsonSerialize();
    }

    protected function getAllInputsInValidState()
    {
        return [
            'title' => 'Foobar\'s Fiddle-Along',
            'year' => (int) 1981,
            'description' => 'The best album, evaaah!',
            'has_explicit_lyrics' => 0,
            'full_album_price' => 999,
        ];
    }

    public function testAllReturnsOkStatusAndExpectedJsonStructure()
    {
        $album = $this->createAlbum();

        $this
            ->json('GET', route('albums.index'))
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    Arr::except($this->getJsonStructure($album), ['artist', 'songs']),
                ],
            ]);
    }

    public function testShowReturnsOkStatusAndExpectedJsonStructure()
    {
        $album = $this->createAlbum();

        $this
            ->json('GET', route('albums.show', ['id' => $album->id]))
            ->assertStatus(200)
            ->assertExactJson([
                'data' => Arr::except($this->getJsonStructure($album), ['artist', 'songs']),
            ]);
    }

    public function testStoreWhenNotAuthenticatedResultsInNotAuthorizedResponse()
    {
        $this->json('POST', route('albums.store'), $this->getAllInputsInValidState())
            ->assertStatus(403);
    }

    public function testStoreWithValidInputReturnsOkStatusAndExpectedJsonStructure()
    {
        $artist = $this->factory($this->artist)->create();

        $album = $this->factory($this->album)->withSongs()->make();

        $album->full_album_price = 999;

        $this->assertInstanceOf(Money::class, $album->full_album_price);

        $albumAsArray = $album->toArray();
        $albumAsArray['full_album_price'] = 999;
        $albumAsArray['artist_id'] = $artist->id;

        $this->actingAs($artist->user)
            ->json('POST', route('albums.store'), $albumAsArray)
            ->assertStatus(201)
            ->assertJson([
                'data' => Arr::except($this->getJsonStructure($album), ['id']),
            ]);
    }

    public function testStoreWithInvalidInputReturnsUnprocessableEntityStatusAndExpectedJsonStructure()
    {
        $artist = $this->factory($this->artist)->create();

        $this->actingAs($artist->user)
            ->json('POST', route('albums.store'), [])
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors',
            ]);
    }

    public function testUpdateWhenNotAuthenticatedResultsInNotAuthorizedResponse()
    {
        $album = $this->createAlbum();

        $this->json(
            'PUT',
            route('albums.update', ['id' => $album->id]),
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
                'data' => Arr::except($this->getJsonStructure($album), ['artist']),
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
                'errors',
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
        $this->json('DELETE', str_replace('foo', '', route('albums.destroy', ['id' => 'foo'])))
            ->assertStatus(405);
    }
}
