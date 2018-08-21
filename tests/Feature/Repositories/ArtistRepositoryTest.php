<?php

namespace Tests\Feature\Repositories;

use App\Contracts\ProfileRepositoryInterface;
use App\Profile;
use App\Contracts\ArtistRepositoryInterface;

class ArtistRepositoryTest extends RepositoryTestCase
{
    protected $profile;

    public function setUp()
    {
        parent::setUp();

        /*
         * Add additional dependencies in the setUp() method AFTER parent::setUp()
         */
        $this->profile = resolve(ProfileRepositoryInterface::class);
    }

    /**
     * Sets the $repo property
     */
    public function setRepository()
    {
        $this->repo = resolve(ArtistRepositoryInterface::class);
    }

    /**
     * Ensure the method create() creates a new record in the database and creates a profile for
     * said Artist
     *
     * @return void
     */
    public function test_method_create_storesNewModel()
    {
        $profile = factory($this->profile->class())->make()->toArray();

        $artist = $this->repo->create($profile);

        $this->assertInstanceOf($this->repo->class(), $artist);
        $this->assertInstanceOf($this->profile->class(), $artist->profile);
    }

    /**
     * Ensure that the update() method updates the model record in the database.
     *
     * @return void
     */
    public function test_method_update_updatesModel()
    {
        $profile = factory($this->profile->class())->make(['country_code' => 'US'])->toArray();

        $artist = $this->repo->create($profile);

        $this->repo->update($artist->id, [
            'country_code' => 'CA',
        ]);

        $this->assertTrue(
            $this->repo->findById($artist->id)->profile->country->code === 'CA'
        );
    }
}
