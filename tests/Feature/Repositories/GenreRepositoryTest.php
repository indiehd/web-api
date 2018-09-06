<?php

namespace Tests\Feature\Repositories;

use DB;

use App\Contracts\GenreRepositoryInterface;

class GenreRepositoryTest extends RepositoryCrudTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->seed('StaticDataSeeder');
    }

    /**
     * @inheritdoc
     */
    public function setRepository()
    {
        $this->repo = resolve(GenreRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function test_method_create_storesNewResource()
    {
        $genre = $this->repo->model()->inRandomOrder()->first();

        $this->assertInstanceOf(
            $this->repo->class(),
            $genre
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_updatesResource()
    {
        $genre = $this->repo->model()->inRandomOrder()->first();

        $newValue = 'Some New Genre';

        $property = 'name';

        $this->repo->update($genre->id, [
            $property => $newValue,
        ]);

        $this->assertTrue(
            $this->repo->findById($genre->id)->{$property} === $newValue
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_returnsModelInstance()
    {
        $genre = factory($this->class())->create();

        $updated = $this->repo->update($genre->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function test_method_delete_deletesResource()
    {
        $genre = $this->repo->model()->inRandomOrder()->first();

        DB::transaction(function () use ($genre) {
            $genre->delete();
        });

        $this->assertNull($this->repo->findById($genre->id));
    }
}
