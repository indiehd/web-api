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

    /**
     * @inheritdoc
     */
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
     * Make a new User object.
     *
     * @param array $userProperties
     * @param array $accountProperties
     * @return \App\User
     */
    public function makeUser(array $userProperties = [], array $accountProperties = [])
    {
        $user = factory($this->user->class())->make($userProperties);

        $account = factory($this->repo->class())->make($accountProperties);

        $user = $this->user->create([
            'username' => $user->username,
            'password' => $user->password,
            'account' => $account->toArray(),
        ]);

        return $user;
    }

    /**
     * @inheritdoc
     */
    public function test_method_create_storesNewResource()
    {
        $user = $this->makeUser();

        // Normally, the User Repository injects this repository and creates
        // the Account. So, to test this repository independently, we'll simply
        // delete the previously-associated account and store a new one.

        $account = factory($this->repo->class())->make(['user_id' => $user->id]);

        $user->account->delete();

        $this->assertInstanceOf(
            $this->repo->class(),
            $this->repo->create(['user_id' => $user->id] + $account->toArray())
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_updatesResource()
    {
        $user = $this->makeUser();

        $newValue = 'Foobius Barius';

        $this->repo->update($user->account->id, [
            'first_name' => $newValue,
        ]);

        $this->assertTrue(
            $this->repo->findById($user->account->id)->first_name === $newValue
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_returnsModelInstance()
    {
        $user = $this->makeUser();

        $updated = $this->repo->update($user->account->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function test_method_delete_deletesResource()
    {
        $user = $this->makeUser();

        $user->account->delete();

        try {
            $this->repo->findById($user->account->id);
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
        $user = $this->makeUser();

        $this->assertInstanceOf($this->user->class(), $user->account->user);
    }

    /**
     * Ensure that an Account with a Country specified at creation belongs to a Country.
     *
     * @return void
     */
    public function test_country_accountBelongsToCountry()
    {
        $user = $this->makeUser([], ['country_code' => 'US']);

        $this->assertInstanceOf($this->country->class(), $user->account->country);
    }
}
