<?php

namespace Tests\Feature\Repositories;

use App\Album;
use App\Artist;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use DatabaseSeeder;

use App\Contracts\AlbumRepositoryInterface;

class AlbumRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var $album AlbumRepositoryInterface
     */
    protected $album;

    public function setUp()
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);

        $this->album = resolve(AlbumRepositoryInterface::class);
    }

    /**
     * Ensure the method class() returns a string.
     *
     * @return void
     */
    public function test_method_class_returnsString()
    {
        $this->assertTrue(is_string($this->album->class()));
    }

    /**
     * Ensure the method class() can be instantiated.
     *
     * @return void
     */
    public function test_method_class_isInstantiable()
    {
        $this->assertInstanceOf(Album::class, resolve($this->album->class()));
    }

    /**
     * Ensure the method model() is an instance of Album.
     *
     * @return void
     */
    public function test_method_model_isInstanceOfAlbum()
    {
        $this->assertInstanceOf($this->album->class(), $this->album->model());
    }

    /**
     * Ensure the method all() returns ONLY a collection of Albums.
     *
     * @return void
     */
    public function test_method_all_returnsOnlyCollectionOfAlbums()
    {
        $albums = $this->album->all();
        $this->assertInstanceOf(Collection::class, $albums);
        $this->assertContainsOnlyInstancesOf($this->album->class(), $albums);
    }

    /**
     * Ensure the method findById() returns a instance of Artist with the id of 1.
     *
     * @return void
     */
    public function test_method_findById_returnsInstanceOfAlbumWithIdOfOne()
    {
        $album = $this->album->findById(1);
        $this->assertInstanceOf($this->album->class(), $album);
        $this->assertTrue($album->id === 1);
    }

    public function test_method_create_storesNewAlbum()
    {
        $album = factory(Album::class)->make([
            'artist_id' => Artist::inRandomOrder()->first()->id
        ])->toArray();

        $this->assertInstanceOf(
            Album::class,
            $this->album->create($album)
        );
    }

    /**
     * Ensure that the update() method updates the model record in the database.
     *
     * @return void
     */
    public function test_method_update_updatesAlbum()
    {
        $artist = Artist::inRandomOrder()->first();

        $album = factory(Album::class)->create([
            'artist_id' => $artist->id
        ]);

        $newTitle = 'Foo Bar';

        $this->album->update($album->id, [
            'title' => $newTitle,
        ]);

        $this->assertTrue(
            $this->album->findById($album->id)->title === $newTitle
        );
    }

    public function test_method_delete_deletesAlbum()
    {
        $artist = Artist::inRandomOrder()->first();

        $album = factory(Album::class)->create([
            'artist_id' => $artist->id
        ]);

        $album->delete();

        $this->assertNull($this->album->findById($album->id));
    }
}
