<?php

namespace Tests\Feature\Repositories;

use App\Contracts\UserRepositoryInterface;
use App\Contracts\AccountRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\CatalogEntityRepositoryInterface;

class UserRepositoryTest extends RepositoryCrudTestCase
{
    /**
     * @var AccountRepositoryInterface $account
     */
    protected $account;

    /**
     * @var UserRepositoryInterface $user
     */
    protected $user;

    /**
     * @var ArtistRepositoryInterface $artist
     */
    protected $artist;

    /**
     * @var CatalogEntityRepositoryInterface $catalogEntity
     */
    protected $catalogEntity;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        $this->seed('CountriesSeeder');

        $this->account = resolve(AccountRepositoryInterface::class);

        $this->user = resolve(UserRepositoryInterface::class);

        $this->artist = resolve(ArtistRepositoryInterface::class);

        $this->catalogEntity = resolve(CatalogEntityRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function setRepository()
    {
        $this->repo = resolve(UserRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function test_method_create_storesNewResource()
    {
        $user = factory($this->repo->class())->make();

        $account = factory($this->account->class())->make()->toArray();

        $this->assertInstanceOf(
            $this->repo->class(),
            $this->repo->create([
                'username' => $user->username,
                'password' => $user->password,
                'account' => $account,
            ])
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_updatesResource()
    {
        $user = factory($this->repo->class())->create();

        $newValue = str_random(32);

        $property = 'username';

        $this->repo->update($user->id, [
            $property => $newValue,
        ]);

        $this->assertTrue(
            $this->repo->findById($user->id)->{$property} === $newValue
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_returnsModelInstance()
    {
        $user = factory($this->repo->class())->create();

        $updated = $this->repo->update($user->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function test_method_delete_deletesResource()
    {
        $user = factory($this->repo->class())->create();

        $user->delete();

        $this->assertNull($this->repo->findById($user->id));
    }

    /**
     * Verify that when a User is associated with a new CatalogEntity, the User has
     * one or more CatalogEntities.
     *
     * @return void
     */
    public function test_entities_whenUserAssociatedWithCatalogEntity_userHasManyCatalogEntities()
    {
        $artist = factory($this->artist->class())->create();

        $catalogEntity = factory($this->catalogEntity->class())->create([
            'user_id' => factory($this->user->class())->create()->id,
            'catalogable_id' => $artist->id,
            'catalogable_type' => $this->artist->class(),
        ]);

        $this->assertInstanceOf($this->catalogEntity->class(), $catalogEntity->user->entities->first());
    }

    /**
     * Verify that when a User is associated with Account, the User has one Account.
     *
     * @return void
     */
    public function test_account_whenUserAssociatedWithAccount_userHasOneAccount()
    {
        $user = factory($this->user->class())/*->states('withAccount')*/->create();

        $this->assertInstanceOf($this->account->class(), $user->account);
    }
}
