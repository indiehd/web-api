<?php

namespace Tests\Feature\Controllers;

use CountriesSeeder;
use App\Contracts\OrderRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\AccountRepositoryInterface;

class OrderControllerTest extends ControllerTestCase
{
    /**
     * @var $order OrderRepositoryInterface
     */
    protected $order;

    /**
     * @var $user UserRepositoryInterface
     */
    protected $user;

    /**
     * @var $account AccountRepositoryInterface
     */
    protected $account;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        $this->seed(CountriesSeeder::class);

        $this->order = resolve(OrderRepositoryInterface::class);

        $this->user = resolve(UserRepositoryInterface::class);

        $this->account = resolve(AccountRepositoryInterface::class);
    }

    /**
     * Define the JSON structure that is expected for the most common responses.
     *
     * @return array
     */
    public function getJsonStructure()
    {
        return [
            'id',
        ];
    }

    /**
     * Generate an exhaustive list of valid inputs for use with test methods
     * that accept inputs.
     *
     * @return array
     */
    public function getAllInputsInValidState()
    {
        return [];
    }

    /**
     * Ensure that a request for the index returns OK HTTP status and the
     * expected JSON structure.
     */
    public function testAllReturnsOkStatusAndExpectedJsonStructure()
    {
        $this->order->create([]);

        $this->json('GET', route('orders.index'))
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    $this->getJsonStructure()
                ]
            ]);
    }

    /**
     * Ensure that a request for an existing record returns OK HTTP status and
     * the expected JSON structure.
     */
    public function testShowReturnsOkStatusAndExpectedJsonStructure()
    {
        $order = $this->order->create([]);

        $this->json('GET', route('orders.show', ['id' => $order->id]))
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->getJsonStructure()
            ]);
    }

    /**
     * Ensure that requests with valid input result in OK HTTP status and the
     * expected JSON structure.
     */
    public function testStoreWithValidInputReturnsOkStatusAndExpectedJsonStructure()
    {
        $this->json('POST', route('orders.store'), $this->getAllInputsInValidState())
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => $this->getJsonStructure()
            ]);
    }

    /**
     * Ensure that any invalid input is ignored and a success response returned
     * in such cases.
     */
    public function testStoreWithInvalidInputReturnsOkStatusAndExpectedJsonStructure()
    {
        $this->json('POST', route('orders.store'), [
            'unexpected' => 'junk',
        ])
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => $this->getJsonStructure()
            ]);
    }

    /**
     * Ensure that any attempt to update an Order fails with an Unauthorized
     * response, as Orders should be immutable once created.
     */
    public function testUpdateReturnsUnauthorizedStatus()
    {
        $order = $this->order->create([]);

        $this->json('PUT', route('orders.update', ['id' => $order->id]))
            ->assertStatus(403);
    }

    /**
     * Ensure that when a valid ID is supplied, the record is destroyed, and an
     * OK HTTP status is returned, along with the expected JSON structure.
     */
    public function testDestroyWithValidInputReturnsOkStatusAndExpectedJsonStructure()
    {
        $order = factory($this->order->class())->create();

        $this->json('DELETE', route('orders.destroy', ['id' => $order->id]))
            ->assertStatus(200)
            ->assertJsonStructure([]);
    }

    /**
     * Ensure that when a invalid ID is supplied, an Unprocessable Entity HTTP
     * status code is returned.
     */
    public function testDestroyWithInvalidInputReturnsUnprocessableEntityStatus()
    {
        $this->json('DELETE', route('orders.destroy', ['id' => 'foo']))
            ->assertStatus(404);
    }

    /**
     * Ensure that when no ID is supplied, a Method Not Allowed HTTP status code
     * is returned.
     */
    public function testDestroyWithMissingInputReturnsMethodNotAllowedStatus()
    {
        $this->json('DELETE', route('orders.destroy', ['id' => null]))
            ->assertStatus(405);
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
            'password' => $user->password,
            'account' => factory($this->account->class())->raw()
        ]);

        return $user;
    }
}
