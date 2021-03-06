<?php

namespace Tests\Feature\Repositories;

use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class RepositoryReadOnlyTestCase extends TestCase
{
    use RefreshDatabase;

    /**
     * @var BaseRepository
     */
    protected $repo;

    public function setUp(): void
    {
        parent::setUp();

        $this->setRepository();
    }

    /**
     * Sets the $repo property.
     *
     * @return void
     */
    abstract public function setRepository();

    public function factory($repo = null): Factory
    {
        $repo = $repo ?: $this->repo;

        return Factory::factoryForModel($repo->class());
    }

    /**
     * Ensure that the $repo property is instantiable.
     *
     * @return void
     */
    public function testRepoIsInstantiable()
    {
        $this->assertTrue($this->isInstantiable($this->repo));
    }

    /**
     * Ensure that the class() method returns a string.
     *
     * @return void
     */
    public function testClassReturnsString()
    {
        $this->assertTrue(is_string($this->repo->class()));
    }

    /**
     * Ensure that the class() method return value can be instantiated.
     *
     * @return void
     */
    public function testClassIsInstantiable()
    {
        $this->assertTrue($this->isInstantiable($this->repo->class()));
    }

    /**
     * Ensure that the model() method returns an instance of the Model class.
     *
     * @return void
     */
    public function testModelIsInstanceOfModel()
    {
        $this->assertInstanceOf($this->repo->class(), $this->repo->model());
    }

    /**
     * Ensure that the all() method returns a Collection containing instances ONLY of Model.
     *
     * @return void
     */
    public function testAllReturnsOnlyCollectionOfModels()
    {
        $models = $this->repo->all();
        $this->assertInstanceOf(Collection::class, $models);
        $this->assertContainsOnlyInstancesOf($this->repo->class(), $models);
    }
}
