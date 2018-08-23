<?php

namespace App\Repositories;

use App\Country;
use App\Contracts\CountryRepositoryInterface;

class CountryRepository extends BaseRepository implements CountryRepositoryInterface
{
    /**
     * @var  string  $class
     */
    protected $class = Country::class;

    /**
     * @var  Country  $country
     */
    protected $country;

    public function __construct(Country $country)
    {
        $this->country = $country;
    }

    public function class()
    {
        return $this->class;
    }

    public function model()
    {
        return $this->country;
    }
}
