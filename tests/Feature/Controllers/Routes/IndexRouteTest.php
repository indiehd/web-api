<?php

namespace Tests\Feature\Controllers\Routes;

use App\Contracts\UserRepositoryInterface;
use Tests\Feature\Controllers\ControllerTestCase;
use CountriesSeeder;

class IndexRouteTest extends ControllerTestCase
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

    public function testRouteReturnsAllResults()
    {
        factory($this->model->class(), 5)->create();

        $this->json('GET', route('users.index'))
            ->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function testRouteReturnsOneOfFiveResults()
    {
        factory($this->model->class(), 5)->create();

        $this->json('GET', route('users.index'), ['limit' => 1])
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function testRouteReturnsPaginatedResults()
    {
        factory($this->model->class(), 10)->create();

        $this->json('GET', route('users.index'), ['paginate' => 5])
            ->assertStatus(200)
            ->assertJsonStructure($this->paginatedJsonStructure())
            ->assertJsonFragment(['per_page' => 5])
            ->assertJsonCount(5, 'data');
    }

    public function testRouteReturnsLimitedPaginatedResults()
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

    public function testRouteReturnsLimitedPaginatedResultsForPageTwo()
    {
        factory($this->model->class(), 10)->create();

        $this->json('GET', route('users.index'), [
            'limit' => 8,
            'paginate' => 3,
            'page' => 2
        ])
            ->assertStatus(200)
            ->assertJsonStructure($this->paginatedJsonStructure())
            ->assertJsonFragment([
                'per_page' => 3,
                'current_page' => 2,
                'total' => 8
            ])
            ->assertJsonCount(3, 'data');
    }

}
