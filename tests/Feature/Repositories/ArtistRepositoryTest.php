<?php

namespace Tests\Feature\Repositories;

use App\Artist;
use App\Profile;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use DatabaseSeeder;

use App\Contracts\ArtistRepositoryInterface;

class ArtistRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var $artist ArtistRepositoryInterface
     */
    protected $artist;

    public function setUp()
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);

        $this->artist = resolve(ArtistRepositoryInterface::class);
    }

    /**
     * Ensure the method class() returns a string.
     *
     * @return void
     */
    public function test_method_class_returnsString()
    {
        $this->assertTrue(is_string($this->artist->class()));
    }

    /**
     * Ensure the method class() can be instantiated.
     *
     * @return void
     */
    public function test_method_class_isInstantiable()
    {
        $this->assertInstanceOf(Artist::class, resolve($this->artist->class()));
    }

    /**
     * Ensure the method model() is an instance of Artist.
     *
     * @return void
     */
    public function test_method_model_isInstanceOfArtist()
    {
        $this->assertInstanceOf($this->artist->class(), $this->artist->model());
    }

    /**
     * Ensure the method all() returns ONLY a collection of Artist.
     *
     * @return void
     */
    public function test_method_all_returnsOnlyCollectionOfArtists()
    {
        $artists = $this->artist->all();
        $this->assertInstanceOf(Collection::class, $artists);
        $this->assertContainsOnlyInstancesOf($this->artist->class(), $artists);
    }

    /**
     * Ensure the method findById() returns a instance of Artist with the id of 1.
     *
     * @return void
     */
    public function test_method_findById_returnsInstanceOfArtistWithIdOfOne()
    {
        $artist = $this->artist->findById(1);
        $this->assertInstanceOf($this->artist->class(), $artist);
        $this->assertTrue($artist->id === 1);
    }

    /**
     * Ensure the method create() creates a new record in the database and creates a profile for
     * said Artist
     *
     * @return void
     */
    public function test_method_create_storesNewArtistAndCreatesProfileForArtist()
    {
        $artist = $this->artist->create([
            'moniker' => 'moniker',
            'city' => 'city',
            'territory' => 'territory',
            'country_code' => 'US',
            'profile_url' => 'profile_url',
        ]);

        $this->assertInstanceOf($this->artist->class(), $artist);
        $this->assertInstanceOf(Profile::class, $artist->profile);
    }

    /**
     * Ensure that the update() method updates the model record in the database.
     *
     * @return void
     */
    public function test_method_update_updatesProfileForArtist()
    {
        $artist = $this->artist->create([
            'moniker' => 'moniker',
            'city' => 'city',
            'territory' => 'territory',
            'country_code' => 'US',
            'profile_url' => 'profile_url',
        ]);

        $this->artist->update($artist->id, [
            'country_code' => 'CA',
        ]);

        $this->assertTrue(
            $this->artist->findById($artist->id)->profile->country->code === 'CA'
        );
    }
}
