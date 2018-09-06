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
            [
                'user_id' => $user->id,
                'email' => 'foobar@example.com',
                'first_name' => 'Foobius',
                'last_name' => 'Barius',
                'address_one' => '123 Any Street',
                'address_two' => 'Apt 1',
                'city' => 'New York',
                'territory' => 'New York',
                'country_code' => 'US',
                'postal_code' => '10110',
                #'phone' => '+1 510 200 3000',
                #'alt_phone' => '',
            ]
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

    public function getAllInputsInValidState()
    {
        return [
            'username' => 'FoobiusBarius',
            'password' => 'secretsauce',
        ];
    }

    public function getInputsInInvalidState()
    {
        return [
            'username' => 'short',
            'password' => 'foo',
        ];
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
                'errors' => array_keys($this->getAllInputsInValidState())
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
                    'account' => $inputs,
                ] + array_flip([
                        'id',
                        'username',
                        'permissions',
                        'last_login',
                    ])
            ]);
    }

    public function test_update_withInvalidInputs_returnsErrorMessage()
    {
        $user = $this->spawnUser();

        $this->json('PUT', route('user.update', ['id' => $user->id]), $this->getInputsInInvalidState())
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => array_keys($this->getAllInputsInValidState())
            ]);
    }
}
