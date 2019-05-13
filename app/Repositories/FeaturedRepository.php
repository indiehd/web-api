<?php

namespace App\Repositories;

use App\Featured;
use App\Contracts\FeaturedRepositoryInterface;

class FeaturedRepository extends CrudRepository implements FeaturedRepositoryInterface
{
    /**
     * @var string $class
     */
    protected $class = Featured::class;

    /**
     * @var Featured
     */
    protected $featured;

    public function __construct(Featured $featured)
    {
        $this->featured = $featured;
    }

    public function class()
    {
        return $this->class;
    }

    public function model()
    {
        return $this->featured;
    }

    public function artists()
    {
        return $this->model()->artists();
    }
}
