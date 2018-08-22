<?php

namespace Tests\Feature\Repositories;

use DB;

use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;

class ArtistRepositoryTest extends RepositoryTestCase
{
    protected $profile;

    public function setUp()
    {
        parent::setUp();

        $this->profile = resolve(ProfileRepositoryInterface::class);

        $this->album = resolve(AlbumRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function setRepository()
    {
        $this->repo = resolve(ArtistRepositoryInterface::class);
    }

    /**
     * Ensure the method create() creates a new record in the database and creates a profile for
     * said Artist.
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
     * @inheritdoc
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

    /**
     * @inheritdoc
     */
    public function test_method_delete_deletesModel()
    {
        $artist = $this->repo->model()->inRandomOrder()->first();

        factory($this->album->class())->create([
            'artist_id' => $artist->id
        ]);

        DB::transaction(function () use ($artist) {
            $artist->delete();
        });

        $this->assertNull($this->repo->findById($artist->id));
    }
}
