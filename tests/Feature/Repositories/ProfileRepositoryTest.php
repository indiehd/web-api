<?php

namespace Tests\Feature\Repositories;

use DB;

use App\Contracts\ArtistRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Contracts\AlbumRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;

class ProfileRepositoryTest extends RepositoryTestCase
{
    use RefreshDatabase;

    /**
     * @var  $album  AlbumRepositoryInterface
     */
    #protected $album;

    /**
     * @var  $artist  ArtistRepositoryInterface
     */
    protected $artist;

    public function setUp()
    {
        parent::setUp();

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
    public function test_method_create_storesNewModel()
    {
        $artist = factory($this->artist->class())->create([]);

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
    public function test_method_update_updatesModel()
    {
        $artist = factory($this->artist->class())->create([]);

        $profile = factory($this->repo->class())->create([
            'profilable_id' => $artist->id,
            'profilable_type' => $this->repo->class(),
        ]);

        $newValue = 'Foo Bar';

        $property = 'moniker';

        $this->repo->update($profile->id, [
            $property => $newValue,
        ]);

        $this->assertTrue(
            $this->repo->findById($profile->id)->{$property} === $newValue
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_delete_deletesModel()
    {
        $artist = factory($this->artist->class())->create([]);

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
