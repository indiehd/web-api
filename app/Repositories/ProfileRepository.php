<?php

namespace App\Repositories;

use App\Contracts\ProfileRepositoryInterface;
use App\Profile;
use Tests\Feature\Repositories\ProfileRepositoryTest;

class ProfileRepository extends CrudRepository implements ProfileRepositoryInterface
{
    /**
     * @var string $class
     */
    protected $class = Profile::class;

    /**
     * @var Profile $profile
     */
    protected $profile;

    public function __construct(Profile $profile)
    {
        $this->profile = $profile;
    }

    /**
     * Returns the class namespace
     *
     * @return string
     */
    public function class()
    {
        return $this->class;
    }

    /**
     * Returns the Repositories Model instance
     *
     * @return Profile
     */
    public function model()
    {
        return $this->profile;
    }

    public function testClass()
    {
        return resolve(ProfileRepositoryTest::class);
    }
}
