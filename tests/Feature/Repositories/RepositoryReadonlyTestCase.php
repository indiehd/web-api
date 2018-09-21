<?php

namespace Tests\Feature\Repositories;

use Tests\TestCase;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class RepositoryReadOnlyTestCase extends TestCase
{
    use RefreshDatabase;

    /**
     * @var $repo
     */
    protected $repo;

    public function setUp()
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

    abstract public function make();

    /**
     * Ensure the property $repo is instantiable.
     *
     * @return void
     */
    public function test_property_repo_isInstantiable()
    {
        $this->assertTrue($this->isInstantiable($this->repo));
    }

    /**
     * Ensure the method class() returns a string.
     *
     * @return void
     */
    public function test_method_class_returnsString()
    {
        $this->assertTrue(is_string($this->repo->class()));
    }

    /**
     * Ensure the method class() can be instantiated.
     *
     * @return void
     */
    public function test_method_class_isInstantiable()
    {
        $this->assertTrue($this->isInstantiable($this->repo->class()));
    }

    /**
     * Ensure the method model() is an instance of Model.
     *
     * @return void
     */
    public function test_method_model_isInstanceOfModel()
    {
        $this->assertInstanceOf($this->repo->class(), $this->repo->model());
    }

    /**
     * Ensure the method all() returns a Collection containing instances ONLY of Model.
     *
     * @return void
     */
    public function test_method_all_returnsOnlyCollectionOfModels()
    {
        $models = $this->repo->all();
        $this->assertInstanceOf(Collection::class, $models);
        $this->assertContainsOnlyInstancesOf($this->repo->class(), $models);
    }
}
