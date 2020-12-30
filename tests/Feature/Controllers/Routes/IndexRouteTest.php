<?php

namespace Tests\Feature\Controllers\Routes;

use App\Contracts\AlbumRepositoryInterface;
use IndieHD\Velkart\Database\Seeders\CountriesSeeder;
use Tests\Feature\Controllers\ControllerTestCase;

class IndexRouteTest extends ControllerTestCase
{
    protected $model;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(CountriesSeeder::class);

        $this->album = resolve(AlbumRepositoryInterface::class);
    }

    private function paginatedJsonStructure()
    {
        return [
            'data',
            'links',
            'meta',
        ];
    }

    public function testRouteReturnsAllResults()
    {
        $this->createAlbums(5);

        $this->json('GET', route('albums.index'))
            ->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function testRouteReturnsOneOfFiveResults()
    {
        $this->createAlbums(5);

        $this->json('GET', route('albums.index'), ['limit' => 1])
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function testRouteReturnsPaginatedResults()
    {
        $this->createAlbums(10);

        $this->json('GET', route('albums.index'), ['paginate' => 5])
            ->assertStatus(200)
            ->assertJsonStructure($this->paginatedJsonStructure())
            ->assertJsonFragment(['per_page' => 5])
            ->assertJsonCount(5, 'data');
    }

    public function testRouteReturnsLimitedPaginatedResults()
    {
        $this->createAlbums(10);

        $this->json('GET', route('albums.index'), [
            'limit' => 8,
            'paginate' => 2,
        ])
            ->assertStatus(200)
            ->assertJsonStructure($this->paginatedJsonStructure())
            ->assertJsonFragment(['total' => 8, 'per_page' => 2])
            ->assertJsonCount(2, 'data');
    }

    public function testRouteReturnsLimitedPaginatedResultsForPageTwo()
    {
        $this->createAlbums(10);

        $this->json('GET', route('albums.index'), [
            'limit' => 8,
            'paginate' => 3,
            'page' => 2,
        ])
            ->assertStatus(200)
            ->assertJsonStructure($this->paginatedJsonStructure())
            ->assertJsonFragment([
                'per_page' => 3,
                'current_page' => 2,
                'total' => 8,
            ])
            ->assertJsonCount(3, 'data');
    }

    protected function createAlbums(int $num)
    {
        return $this->factory($this->album)->times($num)->create([
            'is_active' => true,
            'has_explicit_lyrics' => false,
        ]);
    }
}
