<?php

namespace Tests\Feature\Repositories;

use App\Contracts\AccountRepositoryInterface;
use App\Contracts\CountryRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use CountriesSeeder;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AccountRepositoryTest extends RepositoryCrudTestCase
{
    /**
     * @var UserRepositoryInterface
     */
    protected $user;

    /**
     * @var CountryRepositoryInterface
     */
    protected $country;

    /**
     * @inheritdoc
     */
    public function setUp(): void
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
    public function testCreateStoresNewResource()
    {
        $user = $this->makeUser();

        // Normally, the User Repository injects this repository and creates
        // the Account. So, to test this repository independently, we'll simply
        // delete the previously-associated account and store a new one.

        $account = $this->factory()->make(['user_id' => $user->id]);

        $user->account->delete();

        $this->assertInstanceOf(
            $this->repo->class(),
            $this->repo->create(['user_id' => $user->id] + $account->toArray())
        );
    }

    /**
     * @inheritdoc
     */
    public function testUpdateUpdatesResource()
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
    public function testUpdateReturnsModelInstance()
    {
        $user = $this->makeUser();

        $updated = $this->repo->update($user->account->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function testDeleteDeletesResource()
    {
        $user = $this->makeUser();

        $user->account->delete();

        try {
            $this->repo->findById($user->account->id);
        } catch (ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Ensure that an Account belongs to a User.
     *
     * @return void
     */
    public function testAccountBelongsToUser()
    {
        $user = $this->makeUser();

        $this->assertInstanceOf($this->user->class(), $user->account->user);
    }

    /**
     * Ensure that when a Country is related to an Account, the Account
     * belongs to a Country.
     *
     * @return void
     */
    public function testWhenAccountRelatedToCountryItBelongsToCountry()
    {
        $user = $this->makeUser([], ['country_code' => 'US']);

        $this->assertInstanceOf($this->country->class(), $user->account->country);
    }

    /**
     * Make a new User object.
     *
     * @param array $userProperties
     * @param array $accountProperties
     * @return \App\User
     */
    protected function makeUser(array $userProperties = [], array $accountProperties = [])
    {
        $user = $this->factory($this->user)->make($userProperties);

        $account = $this->factory()->make($accountProperties);

        $user = $this->user->create([
            'email' => $user->email,
            'name' => $user->name,
            'password' => $user->password,
            'account' => $account->toArray(),
        ]);

        return $user;
    }
}
