<?php

namespace Tests\Feature\Repositories;

use DB;

use App\Contracts\CatalogEntityRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\LabelRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

    /**
     * @var $profile ProfileEntityRepositoryInterface
     */
    protected $profile;

    /**
     * @var $label LabelRepositoryInterface
     */
    protected $label;

    public function setUp()
    {
        parent::setUp();

        $this->seed('CountriesSeeder');

        $this->user = resolve(UserRepositoryInterface::class);

        $this->artist = resolve(ArtistRepositoryInterface::class);

        $this->profile = resolve(ProfileRepositoryInterface::class);

        $this->label = resolve(LabelRepositoryInterface::class);
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

        try {
            $this->repo->findById($catalogEntity->id);
        } catch(ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Ensure that models of every CatalogableEntity type morph to a CatalogEntity.
     *
     * @return void
     */
    public function test_catalogable_allTypes_morphToCatalogEntity()
    {
        $artist = factory($this->artist->class())->create();

        factory($this->repo->class())->create([
            'user_id' => factory($this->user->class())->create()->id,
            'catalogable_id' => $artist->id,
            'catalogable_type' => $this->artist->class()
        ]);

        factory($this->profile->class())->create([
            'profilable_id' => $artist->id,
            'profilable_type' => $this->artist->class()
        ]);

        $this->assertInstanceOf($this->repo->class(), $artist->catalogable);

        $label = factory($this->label->class())->create();

        factory($this->repo->class())->create([
            'user_id' => factory($this->user->class())->create()->id,
            'catalogable_id' => $label->id,
            'catalogable_type' => $this->label->class()
        ]);

        factory($this->profile->class())->create([
            'profilable_id' => $label->id,
            'profilable_type' => $this->label->class()
        ]);

        $this->assertInstanceOf($this->repo->class(), $label->catalogable);
    }

    /**
     * Ensure that a new CatalogEntity belongs to a User.
     *
     * @return void
     */
    public function test_user_newCatalogEntity_belongsToUser()
    {
        $artist = factory($this->artist->class())->create();

        factory($this->repo->class())->create([
            'user_id' => factory($this->user->class())->create()->id,
            'catalogable_id' => $artist->id,
            'catalogable_type' => $this->artist->class()
        ]);

        factory($this->profile->class())->create([
            'profilable_id' => $artist->id,
            'profilable_type' => $this->artist->class()
        ]);

        $this->assertInstanceOf($this->user->class(), $artist->catalogable->user);
    }

    /**
     * Ensure that when a CatalogEntity is approved, the CatalogEntity
     * belongs to an approver who is a User.
     */
    public function test_approver_aNewCatalogEntityOfRandomTypeIsApproved_belongsToApprover()
    {
        $catalogEntity = factory($this->repo->class())->make([
            'approver_id' => factory($this->user->class())->create()->id
        ]);

        $this->assertInstanceOf($this->user->class(), $catalogEntity->approver);
    }

    /**
     * Ensure that when a CatalogEntity is deleted, the CatalogEntity
     * belongs to a deleter who is a User.
     */
    public function test_deleter_aNewCatalogEntityOfRandomTypeIsDeleted_belongsToDeleter()
    {
        $catalogEntity = factory($this->repo->class())->make([
            'deleter_id' => factory($this->user->class())->create()->id
        ]);

        $this->assertInstanceOf($this->user->class(), $catalogEntity->deleter);
    }
}
