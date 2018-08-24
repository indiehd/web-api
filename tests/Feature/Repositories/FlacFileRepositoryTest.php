<?php

namespace Tests\Feature\Repositories;

use DB;

use App\Contracts\FlacFileRepositoryInterface;

class FlacFileRepositoryTest extends RepositoryCrudTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

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
    public function test_method_create_storesNewModel()
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
    public function test_method_update_updatesModel()
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
    public function test_method_delete_deletesModel()
    {
        $flacFile = factory($this->repo->class())->create();

        DB::transaction(function () use ($flacFile) {
            $flacFile->delete();
        });

        $this->assertNull($this->repo->findById($flacFile->id));
    }
}
