<?php

namespace App\Repositories;

use App\Contracts\CountryRepositoryInterface;
use App\Country;

class CountryRepository extends BaseRepository implements CountryRepositoryInterface
{
    /**
     * @var  string 
     */
    protected $class = Country::class;

    /**
     * @var  Country 
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
