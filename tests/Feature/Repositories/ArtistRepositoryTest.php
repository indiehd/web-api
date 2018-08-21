<?php

namespace Tests\Feature\Repositories;

use App\Profile;
use App\Contracts\ArtistRepositoryInterface;

class ArtistRepositoryTest extends RepositoryTestCase
{

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
        $profile = factory(Profile::class)->make()->toArray();

        $artist = $this->repo->create($profile);

        $this->assertInstanceOf($this->repo->class(), $artist);
        $this->assertInstanceOf(Profile::class, $artist->profile);
    }

    /**
     * Ensure that the update() method updates the model record in the database.
     *
     * @return void
     */
    public function test_method_update_updatesModel()
    {
        $profile = factory(Profile::class)->make(['country_code' => 'US'])->toArray();

        $artist = $this->repo->create($profile);

        $this->repo->update($artist->id, [
            'country_code' => 'CA',
        ]);

        $this->assertTrue(
            $this->repo->findById($artist->id)->profile->country->code === 'CA'
        );
    }
}
