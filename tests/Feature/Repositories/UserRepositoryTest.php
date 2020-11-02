<?php

namespace Tests\Feature\Repositories;

use App\Contracts\AccountRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\CatalogEntityRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserRepositoryTest extends RepositoryCrudTestCase
{
    /**
     * @var AccountRepositoryInterface
     */
    protected $account;

    /**
     * @var UserRepositoryInterface
     */
    protected $user;

    /**
     * @var ProfileRepositoryInterface
     */
    protected $profile;

    /**
     * @var ArtistRepositoryInterface
     */
    protected $artist;

    /**
     * @var CatalogEntityRepositoryInterface
     */
    protected $catalogEntity;

    /**
     * @inheritdoc
     */
    public function setUp(): void
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
     * @inheritdoc
     */
    public function testCreateStoresNewResource()
    {
        $this->assertInstanceOf(
            $this->repo->class(),
            $this->createUser()
        );
    }

    /**
     * @inheritdoc
     */
    public function testUpdateUpdatesResource()
    {
        $user = $this->createUser();

        $newValue = 'foo@bar.com';

        $property = 'email';

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
    public function testUpdateReturnsModelInstance()
    {
        $user = $this->createUser();

        $updated = $this->repo->update($user->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function testDeleteDeletesResource()
    {
        $user = $this->createUser();

        $user->delete();

        try {
            $this->repo->findById($user->id);
        } catch (ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Verify that when a User is related to a CatalogEntity, the User has
     * one or more CatalogEntities.
     *
     * @return void
     */
    public function testWhenUserRelatedToCatalogEntityItHasManyCatalogEntities()
    {
        $catalogEntity = $this->catalogEntity->create(
            $this->makeCatalogEntity($this->artist)->toArray()
        );

        $this->assertInstanceOf($this->catalogEntity->class(), $catalogEntity->user->entities->first());
    }

    /**
     * Verify that when a User is related to an Account, the User has one Account.
     *
     * @return void
     */
    public function testWhenUserRelatedToAccountItHasOneAccount()
    {
        $user = $this->createUser();

        $this->assertInstanceOf($this->account->class(), $user->account);
    }

    /**
     * Create a User.
     *
     * @return \App\User
     */
    protected function createUser()
    {
        $user = $this->factory($this->user)->make();

        $user = $this->user->create([
            'email' => $user->email,
            'name' => $user->name,
            'password' => $user->password,
            'account' => $this->factory($this->account)->raw(),
        ]);

        return $user;
    }

    /**
     * Make a CatalogEntity.
     *
     * @param $type
     * @param array $entityProperties
     * @param array $catalogableProperties
     * @return mixed
     */
    protected function makeCatalogEntity($type, array $entityProperties = [], array $catalogableProperties = [])
    {
        $user = $this->createUser();

        $profile = $this->factory($this->profile)->make();

        $entity = $type->create(array_merge($profile->toArray(), $entityProperties));

        return $this->factory($this->catalogEntity)->make(array_merge([
            'catalogable_id' => $entity->id,
            'catalogable_type' => $type->class(),
            'user_id' => $user->id,
        ], $catalogableProperties));
    }
}
