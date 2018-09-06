<?php

namespace Tests\Feature\Repositories;

use DB;

use App\Contracts\CatalogEntityRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;

class CatalogEntityRepositoryTest extends RepositoryCrudTestCase
{
    /**
     * @var $user UserRepositoryInterface
     */
    protected $user;

    /**
     * @var $artist ArtistRepositoryInterface
     */
    protected $artist;

    public function setUp()
    {
        parent::setUp();

        $this->seed('CountriesSeeder');

        $this->user = resolve(UserRepositoryInterface::class);

        $this->artist = resolve(ArtistRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function setRepository()
    {
        $this->repo = resolve(CatalogEntityRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function test_method_create_storesNewResource()
    {
        $artist = factory($this->artist->class())->create();

        $catalogEntity = factory($this->repo->class())->make([
            'catalogable_id' => $artist->id,
            'catalogable_type' => $this->repo->class(),
            'user_id' => factory($this->user->class())->create()->id
        ])->toArray();

        $this->assertInstanceOf(
            $this->repo->class(),
            $this->repo->create($catalogEntity)
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_updatesResource()
    {
        $artist = factory($this->artist->class())->create();

        $catalogEntity = factory($this->repo->class())->create([
            'catalogable_id' => $artist->id,
            'catalogable_type' => $this->repo->class(),
            'user_id' => factory($this->user->class())->create()->id
        ]);

        $newValue = 'Foobius Barius';

        $property = 'first_name';

        $this->repo->update($catalogEntity->id, [
            $property => $newValue,
        ]);

        $this->assertTrue(
            $this->repo->findById($catalogEntity->id)->{$property} === $newValue
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_returnsModelInstance()
    {
        $artist = factory($this->artist->class())->create();

        $catalogEntity = factory($this->repo->class())->create([
            'catalogable_id' => $artist->id,
            'catalogable_type' => $this->repo->class(),
            'user_id' => factory($this->user->class())->create()->id
        ]);

        $updated = $this->repo->update($catalogEntity->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function test_method_delete_deletesResource()
    {
        $artist = factory($this->artist->class())->create();

        $catalogEntity = factory($this->repo->class())->create([
            'catalogable_id' => $artist->id,
            'catalogable_type' => $this->repo->class(),
            'user_id' => factory($this->user->class())->create()->id
        ]);

        DB::transaction(function () use ($catalogEntity) {
            $catalogEntity->delete();
        });

        $this->assertNull($this->repo->findById($catalogEntity->id));
    }
}
