<?php

namespace Tests\Feature\Controllers;

use CountriesSeeder;

use App\Contracts\OrderRepositoryInterface;

class OrderControllerTest extends ControllerTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->seed(CountriesSeeder::class);

        $this->order = resolve(OrderRepositoryInterface::class);
    }

    public function getJsonStructure()
    {
        return [
            'id',
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
        return [];
    }

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

    public function testShowReturnsOkStatusAndExpectedJsonStructure()
    {
        $order = $this->order->create([]);

        $this->json('GET', route('orders.show', ['id' => $order->id]))
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->getJsonStructure()
            ]);
    }

    public function testStoreWithValidInputReturnsOkStatusAndExpectedJsonStructure()
    {
        $this->json('POST', route('orders.store'), $this->getAllInputsInValidState())
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => $this->getJsonStructure()
            ]);
    }

    public function testStoreWithInvalidInputReturnsUnprocessableEntityStatusAndExpectedJsonStructure()
    {
        $this->json('POST', route('orders.store'), [])
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors'
            ]);
    }

    public function testUpdateWithValidInputReturnsOkStatusAndExpectedJsonStructure()
    {
        $user = $this->createUser();

        $inputs = $this->getAllInputsInValidState();

        $inputs['username'] = 'FoobiusBazius';

        $this->json('PUT', route('orders.update', ['id' => $user->id]), $inputs)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => $this->getJsonStructure()
            ]);
    }

    public function testUpdateWithInvalidInputReturnsUnprocessableEntityStatusAndExpectedJsonStructure()
    {
        $user = $this->createUser();

        $this->json('PUT', route('orders.update', ['id' => $user->id]), ['email' => ''])
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors'
            ]);
    }

    public function testDestroyWithValidInputReturnsOkStatusAndExpectedJsonStructure()
    {
        $user = $this->createUser();

        $this->json('DELETE', route('orders.destroy', ['id' => $user->id]))
            ->assertStatus(200)
            ->assertJsonStructure([]);
    }

    public function testDestroyWithInvalidInputReturnsUnprocessableEntityStatus()
    {
        $this->json('DELETE', route('orders.destroy', ['id' => 'foo']))
            ->assertStatus(404);
    }

    public function testDestroyWithMissingInputReturnsMethodNotAllowedStatus()
    {
        $this->json('DELETE', route('orders.destroy', ['id' => null]))
            ->assertStatus(405);
    }
}
