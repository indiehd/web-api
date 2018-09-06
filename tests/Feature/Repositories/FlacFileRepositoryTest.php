<?php

namespace Tests\Feature\Repositories;

use DB;

use App\Contracts\FlacFileRepositoryInterface;

class FlacFileRepositoryTest extends RepositoryCrudTestCase
{
    /**
     * @inheritdoc
     */
    public function setRepository()
    {
        $this->repo = resolve(FlacFileRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function test_method_create_storesNewResource()
    {
        $flacFile = factory($this->repo->class())->make()->toArray();

        $this->assertInstanceOf(
            $this->repo->class(),
            $this->repo->create($flacFile)
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_updatesResource()
    {
        $flacFile = factory($this->repo->class())->create();

        $newValue = str_random(32);

        $property = 'md5_data_source';

        $this->repo->update($flacFile->id, [
            $property => $newValue,
        ]);

        $this->assertTrue(
            $this->repo->findById($flacFile->id)->{$property} === $newValue
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_returnsModelInstance()
    {
        $flacFile = factory($this->repo->class())->create();

        $updated = $this->repo->update($flacFile->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function test_method_delete_deletesResource()
    {
        $flacFile = factory($this->repo->class())->create();

        DB::transaction(function () use ($flacFile) {
            $flacFile->delete();
        });

        $this->assertNull($this->repo->findById($flacFile->id));
    }
}
