<?php

namespace Tests\Feature\Repositories;

use CountriesSeeder;
use App\Contracts\AccountRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\CountryRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AccountRepositoryTest extends RepositoryCrudTestCase
{
    /**
     * @var $user UserRepositoryInterface
     */
    protected $user;

    /**
     * @var $country CountryRepositoryInterface
     */
    protected $country;

    public function setUp()
    {
        parent::setUp();

        $this->seed(CountriesSeeder::class);

        $this->user = resolve(UserRepositoryInterface::class);

        $this->country = resolve(CountryRepositoryInterface::class);
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

        try {
            $this->repo->findById($account->id);
        } catch(ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Ensure that a newly-created Account belongs to a User.
     *
     * @return void
     */
    public function test_user_accountBelongsToUser()
    {
        $account = factory($this->repo->class())->create([
            'user_id' => factory($this->user->class())->create()->id
        ]);

        $this->assertInstanceOf($this->user->class(), $account->user);
    }

    /**
     * Ensure that an Account with a Country specified at creation belongs to a Country.
     *
     * @return void
     */
    public function test_country_accountBelongsToCountry()
    {
        $account = factory($this->repo->class())->create([
            'user_id' => factory($this->user->class())->create()->id,
            'country_code' => 'US',
        ]);

        $this->assertInstanceOf($this->country->class(), $account->country);
    }
}
