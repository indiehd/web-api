<?php

namespace Tests\Feature\Controllers;

use App\Contracts\UserRepositoryInterface;
use CountriesSeeder;

class ApiControllerTest extends ControllerTestCase
{
    protected $model;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(CountriesSeeder::class);

        // To test the abstract ApiController we will use the User Model (as its the most likely to never change)
        $this->model = resolve(UserRepositoryInterface::class);
    }

    private function paginatedJsonStructure()
    {
        return [
            'data',
            'links',
            'meta'
        ];
    }

    public function testApiControllerIndexMethodReturnsAllModels()
    {
        factory($this->model->class(), 5)->create();

        $this->json('GET', route('users.index'))
            ->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function testApiControllerIndexMethodReturnsOneOfFiveModels()
    {
        factory($this->model->class(), 5)->create();

        $this->json('GET', route('users.index'), ['limit' => 1])
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function testApiControllerIndexMethodReturnsPaginatedModels()
    {
        factory($this->model->class(), 10)->create();

        $this->json('GET', route('users.index'), ['paginate' => 5])
            ->assertStatus(200)
            ->assertJsonStructure($this->paginatedJsonStructure())
            ->assertJsonFragment(['per_page' => 5])
            ->assertJsonCount(5, 'data');
    }

    public function testApiControllerIndexMethodReturnsLimitedPaginatedModels()
    {
        factory($this->model->class(), 10)->create();

        $this->json('GET', route('users.index'), [
            'limit' => 8,
            'paginate' => 2
        ])
            ->assertStatus(200)
            ->assertJsonStructure($this->paginatedJsonStructure())
            ->assertJsonFragment(['total' => 8, 'per_page' => 2])
            ->assertJsonCount(2, 'data');
    }

}
