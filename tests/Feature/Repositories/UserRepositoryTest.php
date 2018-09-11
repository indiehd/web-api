<?php

namespace Tests\Feature\Repositories;

use App\Contracts\AccountRepositoryInterface;
use App\Contracts\UserRepositoryInterface;

class UserRepositoryTest extends RepositoryCrudTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->seed('CountriesSeeder');
    }

    /**
     * @inheritdoc
     */
    public function setRepository()
    {
        $this->repo = resolve(UserRepositoryInterface::class);

        $this->account = resolve(AccountRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function test_method_create_storesNewResource()
    {
        $user = factory($this->repo->class())->make();

        $account = factory(get_class($this->repo->account))->make()->toArray();

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
}
