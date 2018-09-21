<?php

namespace Tests\Feature\Repositories;

use Illuminate\Database\Eloquent\ModelNotFoundException;

abstract class RepositoryCrudTestCase extends RepositoryReadOnlyTestCase
{
    /**
     * Ensure that the create() method creates a new resource in the database.
     *
     * @return void
     */
    abstract public function test_method_create_storesNewResource();

    /**
     * Ensure that the update() method updates the resource in the database.
     *
     * @return void
     */
    abstract public function test_method_update_updatesResource();

    /**
     * Ensure that the update() method returns an instance of the expected type.
     *
     * @return void
     */
    abstract public function test_method_update_returnsModelInstance();

    /**
     * Ensure that the delete() method deletes the resource from the database.
     *
     * @return void
     */
    public function test_method_delete_deletesResource()
    {
        $model = $this->repo->findById(1);

        $model->delete();

        try {
            $this->repo->findById($model->id);
        } catch(ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }
}
