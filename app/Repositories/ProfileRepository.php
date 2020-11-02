<?php

namespace App\Repositories;

use App\Contracts\ProfileRepositoryInterface;
use App\Profile;

class ProfileRepository extends CrudRepository implements ProfileRepositoryInterface
{
    /**
     * @var string
     */
    protected $class = Profile::class;

    /**
     * @var Profile
     */
    protected $profile;

    public function __construct(Profile $profile)
    {
        $this->profile = $profile;
    }

    /**
     * Returns the class namespace.
     *
     * @return string
     */
    public function class()
    {
        return $this->class;
    }

    /**
     * Returns the Repositories Model instance.
     *
     * @return Profile
     */
    public function model()
    {
        return $this->profile;
    }
}
