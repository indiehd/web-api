<?php

namespace Tests\Feature\Controllers;

use CountriesSeeder;

use App\Contracts\CountryRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\AccountRepositoryInterface;

class UserControllerTest extends ControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->seed(CountriesSeeder::class);

        $this->country = resolve(CountryRepositoryInterface::class);
        $this->user = resolve(UserRepositoryInterface::class);
        $this->account = resolve(AccountRepositoryInterface::class);
    }

    public function spawnUser()
    {
        $user = $this->user->model()->create($this->getAllInputsInValidState());

        $this->account->model()->create(
            ['user_id' => $user->id] + $this->getAllAccountInputsInValidState()
        );

        return $user;
    }

    public function getJsonStructure()
    {
        return [
            'id',
            'username',
            'account',
        ];
    }

    public function getAllAccountInputsInValidState()
    {
        return [
            'email' => 'foobar@example.com',
            'first_name' => 'Foobius',
            'last_name' => 'Barius',
            'address_one' => '123 Any Street',
            'address_two' => 'Apt 1',
            'city' => 'New York',
            'territory' => 'New York',
            'country_code' => 'US',
            'postal_code' => '10110',
            'phone' => '+1 510 200 3000',
            'alt_phone' => null,
        ];
    }

    public function getAllInputsInValidState()
    {
        return [
            'username' => 'FoobiusBarius',
            'password' => 'secretsauce',
            'account' => $this->getAllAccountInputsInValidState()
        ];
    }

    public function getInputsInInvalidState()
    {
        return [
            'username' => 'short',
            'password' => 'foo',
            'account' => $this->getAllAccountInputsInInvalidState(),
        ];
    }

    public function getAllAccountInputsInInvalidState()
    {
        return [
            'email' => 'foo@',
            'first_name' => str_random(65),
            'last_name' => str_random(65),
            'address_one' => str_random(256),
            'address_two' => str_random(256),
            'city' => str_random(65),
            'territory' => str_random(65),
            'country_code' => 'United States',
            'postal_code' => str_random(65),
            'phone' => str_random(65),
            'alt_phone' => str_random(65),
        ];
    }

    public function getErrorMessageKeys()
    {
        $errorMessageKeys = array_diff_key($this->getAllInputsInValidState(), array_flip(['account']));

        foreach ($this->getAllAccountInputsInInvalidState() as $k => $v) {
            $errorMessageKeys['account.' . $k] = null;
        }

        return $errorMessageKeys;
    }

    public function test_index_returnsMultipleJsonObjects()
    {
        $this->spawnUser();

        $this->json('GET', route('user.index'))
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    $this->getJsonStructure()
                ]
            ]);
    }

    public function test_store_withValidInputs_returnsOneJsonObject()
    {
        $this->json('POST', route('user.store'), $this->getAllInputsInValidState())
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => $this->getJsonStructure()
            ]);
    }

    public function test_store_withInvalidInputs_returnsErrorMessage()
    {
        $this->json('POST', route('user.store'), $this->getInputsInInvalidState())
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => array_keys($this->getErrorMessageKeys())
            ]);
    }

    public function test_show_returnsOneJsonObject()
    {
        $user = $this->spawnUser();

        $this->json('GET', route('user.show', ['id' => $user->id]))
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->getJsonStructure()
            ]);
    }

    public function test_update_withValidInputs_returnsJsonObjectMatchingInputs()
    {
        $user = $this->spawnUser();

        $inputs = $this->getAllInputsInValidState();

        $inputs['username'] = 'FoobiusBazius';

        $this->json('PUT', route('user.update', ['id' => $user->id]), $inputs)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $user->id,
                    'username' => $inputs['username'],
                    'account' => $this->getAllAccountInputsInValidState(),
                    'permissions' => [],
                    'last_login' => null,
                ]
            ]);
    }

    public function test_update_withInvalidInputs_returnsErrorMessage()
    {
        $user = $this->spawnUser();

        $this->json('PUT', route('user.update', ['id' => $user->id]), $this->getInputsInInvalidState())
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => array_keys($this->getErrorMessageKeys())
            ]);
    }
}
