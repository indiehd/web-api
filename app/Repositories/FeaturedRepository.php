<?php

namespace App\Repositories;

use App\Featured;
use App\Contracts\FeaturedRepositoryInterface;
use App\Contracts\FeaturableRepositoryInterface;

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

    public function makeFeatured(FeaturableRepositoryInterface $featurable): void
    {
        $eligible = $featurable->featurable()->get();

        $eligible->each(function ($item, $key) use ($featurable) {
            $this->create([
                'featurable_type' => $featurable->class(),
                'featurable_id' => $item->id,
            ]);
        });
    }
}
