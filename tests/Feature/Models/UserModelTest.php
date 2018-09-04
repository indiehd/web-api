<?php

namespace Tests\Feature\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use DatabaseSeeder;
use App\CatalogEntity;
use App\User;
use App\Artist;
use App\Account;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Verify that when a User is associated with a new CatalogEntity, the User has
     * one or more CatalogEntities.
     *
     * @return void
     */
    public function test_entities_whenUserAssociatedWithCatalogEntity_userHasManyCatalogEntities()
    {
        $artist = factory(Artist::class)->create();

        $catalogEntity = factory(CatalogEntity::class)->create([
            'user_id' => factory(User::class)->create()->id,
            'catalogable_id' => $artist->id,
            'catalogable_type' => get_class($artist),
        ]);

        $this->assertInstanceOf(CatalogEntity::class, $catalogEntity->user->entities->first());
    }

    /**
     * Verify that when a User is associated with Account, the User has one Account.
     *
     * @return void
     */
    public function test_account_whenUserAssociatedWithAccount_userHasOneAccount()
    {
        $user = factory(User::class)/*->states('withAccount')*/->create();

        $this->assertInstanceOf(Account::class, $user->account);
    }
}
