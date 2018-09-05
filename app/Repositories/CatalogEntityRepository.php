<?php

namespace App\Repositories;

use App\Contracts\CatalogEntityRepositoryInterface;
use App\CatalogEntity;

class CatalogEntityRepository extends BaseRepository implements CatalogEntityRepositoryInterface
{
    /**
     * @var string $class
     */
    protected $class = CatalogEntity::class;

    /**
     * @var CatalogEntity $catalogEntity
     */
    protected $catalogEntity;

    public function __construct(CatalogEntity $catalogEntity)
    {
        $this->catalogEntity = $catalogEntity;
    }

    public function class()
    {
        return $this->class;
    }

    public function model()
    {
        return $this->catalogEntity;
    }

    public function findById($id)
    {
        return $this->model()->find($id);
    }

    public function create(array $data)
    {
        return $this->model()->create($data);
    }

    public function update($id, array $data)
    {
        $this->findById($id)->update($data);

        return $this->findById($id);
    }
}
