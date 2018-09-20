<?php

namespace App\Repositories;

use App\Contracts\CatalogEntityRepositoryInterface;
use App\CatalogEntity;

class CatalogEntityRepository extends CrudRepository implements CatalogEntityRepositoryInterface
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
}
