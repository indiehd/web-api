<?php

namespace Tests\Feature\Repositories;

use DB;
use Illuminate\Foundation\Testing\WithFaker;

use App\Contracts\UserRepositoryInterface;

class UserRepositoryTest extends RepositoryCrudTestCase
{
    use WithFaker;

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
    public function test_method_create_storesNewModel()
    {
        $user = factory($this->repo->class())->make();

        $this->assertInstanceOf(
            $this->repo->class(),
            $this->repo->create([
                'username' => $user->username,
                'password' => $user->password,
            ])
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_updatesModel()
    {
        $user = factory($this->repo->class())->create();

        $newValue = $this->faker->userName();

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
    public function test_method_delete_deletesModel()
    {
        $user = factory($this->repo->class())->create();

        DB::transaction(function () use ($user) {
            $user->delete();
        });

        $this->assertNull($this->repo->findById($user->id));
    }
}
