<?php

namespace Tests\Feature\Repositories;

use App\Contracts\UserRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\AccountRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\CatalogEntityRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
     * @var ProfileRepositoryInterface $profile
     */
    protected $profile;

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

        $this->profile = resolve(ProfileRepositoryInterface::class);

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
     * Create a new User object.
     *
     * @return \App\User
     */
    public function createUser()
    {
        $user = factory($this->user->class())->make();

        $user = $this->user->create([
            'username' => $user->username,
            'password' => $user->password,
            'account' => factory($this->account->class())->make()->toArray()
        ]);

        return $user;
    }

    public function makeCatalogEntity($type, array $entityProperties = [], array $catalogableProperties = [])
    {
        $user = $this->createUser();

        $profile = factory($this->profile->class())->make();

        $entity = $type->create(array_merge($profile->toArray(), $entityProperties));

        return factory($this->catalogEntity->class())->make(array_merge([
            'catalogable_id' => $entity->id,
            'catalogable_type' => $type->class(),
            'user_id' => $user->id
        ], $catalogableProperties));
    }

    /**
     * @inheritdoc
     */
    public function test_method_create_storesNewResource()
    {
        $this->assertInstanceOf(
            $this->repo->class(),
            $this->createUser()
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_updatesResource()
    {
        $user = $this->createUser();

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
        $user = $this->createUser();

        $updated = $this->repo->update($user->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function test_method_delete_deletesResource()
    {
        $user = $this->createUser();

        $user->delete();

        try {
            $this->repo->findById($user->id);
        } catch(ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Verify that when a User is associated with a new CatalogEntity, the User has
     * one or more CatalogEntities.
     *
     * @return void
     */
    public function test_entities_whenUserAssociatedWithCatalogEntity_userHasManyCatalogEntities()
    {
        $catalogEntity = $this->catalogEntity->create(
            $this->makeCatalogEntity($this->artist)->toArray()
        );

        $this->assertInstanceOf($this->catalogEntity->class(), $catalogEntity->user->entities->first());
    }

    /**
     * Verify that when a User is associated with Account, the User has one Account.
     *
     * @return void
     */
    public function test_account_whenUserAssociatedWithAccount_userHasOneAccount()
    {
        $user = $this->createUser();

        $this->assertInstanceOf($this->account->class(), $user->account);
    }
}
