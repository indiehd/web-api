<?php

namespace Tests\Feature\Repositories;

use CountriesSeeder;
use App\Contracts\AccountRepositoryInterface;
use App\Contracts\UserRepositoryInterface;

class AccountRepositoryTest extends RepositoryCrudTestCase
{
    /**
     * @var $user UserRepositoryInterface
     */
    protected $user;

    public function setUp()
    {
        parent::setUp();

        $this->seed(CountriesSeeder::class);

        $this->user = resolve(UserRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function setRepository()
    {
        $this->repo = resolve(AccountRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function test_method_create_storesNewResource()
    {
        $account = factory($this->repo->class())->make([
            'user_id' => factory($this->user->class())->create()->id
        ])->toArray();

        $this->assertInstanceOf(
            $this->repo->class(),
            $this->repo->create($account)
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_updatesResource()
    {
        $account = factory($this->repo->class())->create([
            'user_id' => factory($this->user->class())->create()->id
        ]);

        $newValue = 'Foobius Barius';

        $this->repo->update($account->id, [
            'first_name' => $newValue,
        ]);

        $this->assertTrue(
            $this->repo->findById($account->id)->first_name === $newValue
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_returnsModelInstance()
    {
        $account = factory($this->repo->class())->create([
            'user_id' => factory($this->user->class())->create()->id
        ]);

        $updated = $this->repo->update($account->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function test_method_delete_deletesResource()
    {
        $account = factory($this->repo->class())->make([
            'user_id' => factory($this->user->class())->create()->id
        ]);

        $account->delete();

        $this->assertNull($this->repo->findById($account->id));
    }
}
