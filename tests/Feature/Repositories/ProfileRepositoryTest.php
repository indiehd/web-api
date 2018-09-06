<?php

namespace Tests\Feature\Repositories;

use DB;

use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;

class ProfileRepositoryTest extends RepositoryCrudTestCase
{
    /**
     * @var  $artist  ArtistRepositoryInterface
     */
    protected $artist;

    public function setUp()
    {
        parent::setUp();

        $this->seed('CountriesSeeder');

        $this->artist = resolve(ArtistRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function setRepository()
    {
        $this->repo = resolve(ProfileRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function test_method_create_storesNewResource()
    {
        $artist = factory($this->artist->class())->create();

        $profile = factory($this->repo->class())->make([
            'profilable_id' => $artist->id,
            'profilable_type' => $this->repo->class(),
        ])->toArray();

        $this->assertInstanceOf(
            $this->repo->class(),
            $this->repo->create($profile)
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_updatesResource()
    {
        $artist = factory($this->artist->class())->create();

        $profile = factory($this->repo->class())->create([
            'profilable_id' => $artist->id,
            'profilable_type' => $this->repo->class(),
        ]);

        $newValue = 'Foobius Barius';

        $property = 'moniker';

        $profile = $this->repo->update($profile->id, [
            $property => $newValue,
        ]);

        $this->assertTrue(
            $profile->fresh()->{$property} === $newValue
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_returnsModelInstance()
    {
        $artist = factory($this->artist->class())->create();

        $profile = factory($this->repo->class())->create([
            'profilable_id' => $artist->id,
            'profilable_type' => $this->repo->class(),
        ]);

        $updated = $this->repo->update($profile->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function test_method_delete_deletesResource()
    {
        $artist = factory($this->artist->class())->create();

        $profile = factory($this->repo->class())->create([
            'profilable_id' => $artist->id,
            'profilable_type' => $this->repo->class(),
        ]);

        DB::transaction(function () use ($profile) {
            $profile->delete();
        });

        $this->assertNull($this->repo->findById($profile->id));
    }
}
