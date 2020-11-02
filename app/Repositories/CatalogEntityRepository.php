<?php

namespace App\Repositories;

use App\CatalogEntity;
use App\Contracts\CatalogEntityRepositoryInterface;

class CatalogEntityRepository extends CrudRepository implements CatalogEntityRepositoryInterface
{
    /**
     * @var string
     */
    protected $class = CatalogEntity::class;

    /**
     * @var CatalogEntity
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
