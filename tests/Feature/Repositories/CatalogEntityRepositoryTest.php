<?php

namespace Tests\Feature\Repositories;

use DB;

use App\Contracts\CatalogEntityRepositoryInterface;
use App\Contracts\AccountRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\LabelRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CatalogEntityRepositoryTest extends RepositoryCrudTestCase
{
    /**
     * @var $account AccountRepositoryInterface
     */
    protected $account;

    /**
     * @var $user UserRepositoryInterface
     */
    protected $user;

    /**
     * @var $artist ArtistRepositoryInterface
     */
    protected $artist;

    /**
     * @var $profile ProfileRepositoryInterface
     */
    protected $profile;

    /**
     * @var $label LabelRepositoryInterface
     */
    protected $label;

    /**
     * @inheritdoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seed('CountriesSeeder');

        $this->account = resolve(AccountRepositoryInterface::class);

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
    public function testCreateStoresNewResource()
    {
        $catalogEntity = $this->makeCatalogEntity($this->artist);

        $this->assertInstanceOf(
            $this->repo->class(),
            $this->repo->create($catalogEntity->toArray())
        );
    }

    /**
     * @inheritdoc
     */
    public function testUpdateUpdatesResource()
    {
        $catalogEntity = $this->repo->create(
            $this->makeCatalogEntity($this->artist)->toArray()
        );

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
    public function testUpdateReturnsModelInstance()
    {
        $catalogEntity = $this->repo->create(
            $this->makeCatalogEntity($this->artist)->toArray()
        );

        $updated = $this->repo->update($catalogEntity->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function testDeleteDeletesResource()
    {
        $catalogEntity = $this->repo->create(
            $this->makeCatalogEntity($this->artist)->toArray()
        );

        $catalogEntity->delete();

        try {
            $this->repo->findById($catalogEntity->id);
        } catch (ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Ensure that models of every CatalogableEntity type morph to a CatalogEntity.
     *
     * @return void
     */
    public function testAllTypesMorphToCatalogEntity()
    {
        $artistEntity = $this->repo->create(
            $this->makeCatalogEntity($this->artist)->toArray()
        );

        $this->assertInstanceOf($this->repo->class(), $artistEntity);

        $labelEntity = $this->repo->create(
            $this->makeCatalogEntity($this->label)->toArray()
        );

        $this->assertInstanceOf($this->repo->class(), $labelEntity);
    }

    /**
     * Ensure that a CatalogEntity belongs to a User.
     *
     * @return void
     */
    public function testCatalogEntityBelongsToUser()
    {
        $artistEntity = $this->repo->create(
            $this->makeCatalogEntity($this->artist)->toArray()
        );

        $this->assertInstanceOf($this->user->class(), $artistEntity->user);
    }

    /**
     * Ensure that when a CatalogEntity is approved, the CatalogEntity
     * belongs to an approver who is a User.
     */
    public function testWhenCatalogEntityIsApprovedItBelongsToApprover()
    {
        $user = $this->createUser();

        $catalogEntity = $this->makeCatalogEntity(
            $this->artist,
            [],
            ['approver_id' => $user->id]
        )->toArray();

        $artistEntity = $this->repo->create($catalogEntity);

        $this->assertInstanceOf($this->user->class(), $artistEntity->approver);
    }

    /**
     * Ensure that when a CatalogEntity is deleted, the CatalogEntity
     * belongs to a deleter who is a User.
     */
    public function testWhenCatalogEntityIsDeletedItBelongsToDeleter()
    {
        $user = $this->createUser();

        $catalogEntity = $this->makeCatalogEntity(
            $this->artist,
            [],
            ['deleter_id' => $user->id]
        )->toArray();

        $artistEntity = $this->repo->create($catalogEntity);

        $this->assertInstanceOf($this->user->class(), $artistEntity->deleter);
    }

    /**
     * Create a User.
     *
     * @return \App\User
     */
    protected function createUser()
    {
        $user = factory($this->user->class())->make();

        $user = $this->user->create([
            'email' => $user->email,
            'name' => $user->name,
            'password' => $user->password,
            'account' => factory($this->account->class())->raw()
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

        $profile = factory($this->profile->class())->make();

        $entity = $type->create(array_merge($profile->toArray(), $entityProperties));

        return factory($this->repo->class())->make(array_merge([
            'catalogable_id' => $entity->id,
            'catalogable_type' => $type->class(),
            'user_id' => $user->id
        ], $catalogableProperties));
    }
}
