<?php

namespace Tests\Feature\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\LabelRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use CountriesSeeder;

use App\CatalogEntity;

class CatalogEntityModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var $artist ArtistRepositoryInterface
     */
    protected $artist;

    /**
     * @var $user UserRepositoryInterface
     */
    protected $user;

    /**
     * @var $profile ProfileRepositoryInterface
     */
    protected $profile;

    /**
     * @var $label LabelRepositoryInterface
     */
    protected $label;

    public function setUp()
    {
        parent::setUp();

        $this->seed(CountriesSeeder::class);

        $this->profile = resolve(ProfileRepositoryInterface::class);

        $this->artist = resolve(ArtistRepositoryInterface::class);

        $this->label = resolve(LabelRepositoryInterface::class);

        $this->user = resolve(UserRepositoryInterface::class);
    }

    /**
     * Ensure that models of every CatalogableEntity type morph to a CatalogEntity.
     *
     * @return void
     */
    public function test_catalogable_allTypes_morphToCatalogEntity()
    {
        $artist = factory($this->artist->class())->create();

        factory(CatalogEntity::class)->create([
            'user_id' => factory($this->user->class())->create()->id,
            'catalogable_id' => $artist->id,
            'catalogable_type' => $this->artist->class()
        ]);

        factory($this->profile->class())->create([
            'profilable_id' => $artist->id,
            'profilable_type' => $this->artist->class()
        ]);

        $this->assertInstanceOf(CatalogEntity::class, $artist->catalogable);

        $label = factory($this->label->class())->create();

        factory(CatalogEntity::class)->create([
            'user_id' => factory($this->user->class())->create()->id,
            'catalogable_id' => $label->id,
            'catalogable_type' => $this->label->class()
        ]);

        factory($this->profile->class())->create([
            'profilable_id' => $label->id,
            'profilable_type' => $this->label->class()
        ]);

        $this->assertInstanceOf(CatalogEntity::class, $label->catalogable);
    }

    /**
     * Ensure that a new CatalogEntity belongs to a User.
     *
     * @return void
     */
    public function test_user_newCatalogEntity_belongsToUser()
    {
        $artist = factory($this->artist->class())->create();

        factory(CatalogEntity::class)->create([
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
     * Ensure that when any random CatalogEntity is approved, the CatalogEntity
     * belongs to an approver who is a User.
     */
    public function test_approver_aNewCatalogEntityOfRandomTypeIsApproved_belongsToApprover()
    {
        $catalogEntity = factory(CatalogEntity::class)->make([
            'approver_id' => factory($this->user->class())->create()->id
        ]);

        $this->assertInstanceOf($this->user->class(), $catalogEntity->approver);
    }

    /**
     * Ensure that when any random CatalogEntity is deleted, the CatalogEntity
     * belongs to a deleter who is a User.
     */
    public function test_deleter_aNewCatalogEntityOfRandomTypeIsDeleted_belongsToDeleter()
    {
        $catalogEntity = factory(CatalogEntity::class)->make([
            'deleter_id' => factory($this->user->class())->create()->id
        ]);

        $this->assertInstanceOf($this->user->class(), $catalogEntity->deleter);
    }
}
