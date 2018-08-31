<?php

namespace Tests\Feature\Repositories;

abstract class RepositoryCrudTestCase extends RepositoryReadOnlyTestCase
{
    /**
     * Ensure the create() method creates a new record in the database.
     *
     * @return void
     */
    abstract public function test_method_create_storesNewModel();

    /**
     * Ensure that the update() method updates the model record in the database.
     *
     * @return void
     */
    abstract public function test_method_update_updatesModel();

    /**
     * Ensure that the delete() method deletes the model record from the database.
     *
     * @return void
     */
    public function test_method_delete_deletesModel()
    {
        $model = $this->repo->findById(1);

        $model->delete();

        $this->assertNull($this->repo->findById($model->id));
    }
}
