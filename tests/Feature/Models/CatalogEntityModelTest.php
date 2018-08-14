<?php

namespace Tests\Feature\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use DatabaseSeeder;

use DB;
use App\User;
use App\CatalogEntity;

class CatalogEntityModelTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Ensure that models of every CatalogableEntity type in the database morph
     * to a CatalogEntity.
     *
     * @return void
     */
    public function test_catalogable_allDistinctTypes_morphToCatalogEntity()
    {
        $catalogableTypes = DB::table('catalog_entities')
            ->select('catalogable_type')
            ->distinct('catalogable_type')
            ->groupBy('catalogable_type')
            ->get();

        foreach ($catalogableTypes as $type) {
            $randomModelOfType = (new $type->catalogable_type)::inRandomOrder()->first();

            $this->assertInstanceOf(CatalogEntity::class, $randomModelOfType->catalogable);
        }
    }

    /**
     * Ensure that any random CatalogEntity belongs to a User.
     *
     * @return void
     */
    public function test_user_anyRandomCatalogEntity_hasOneUser()
    {
        $this->assertInstanceOf(User::class, CatalogEntity::inRandomOrder()->first()->user);
    }

    /**
     * Ensure that when any random CatalogEntity is approved, the CatalogEntity
     * belongs to an approver who is a User.
     */
    public function test_approver_aNewCatalogEntityOfRandomTypeIsApproved_belongsToApprover()
    {
        $catalogEntity = factory(CatalogEntity::class)->make([
            'approver_id' => factory(User::class)->create()->id
        ]);

        $this->assertInstanceOf(User::class, $catalogEntity->approver);
    }

    /**
     * Ensure that when any random CatalogEntity is deleted, the CatalogEntity
     * belongs to a deleter who is a User.
     */
    public function test_deleter_aNewCatalogEntityOfRandomTypeIsDeleted_belongsToDeleter()
    {
        $catalogEntity = factory(CatalogEntity::class)->make([
            'deleter_id' => factory(User::class)->create()->id
        ]);

        $this->assertInstanceOf(User::class, $catalogEntity->deleter);
    }
}
