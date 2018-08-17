<?php

namespace App\Repositories;

use App\Contracts\ProfileRepositoryInterface;
use App\Profile;

class ProfileRepository implements ProfileRepositoryInterface
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

    public function class()
    {
        return $this->class;
    }

    public function create(array $data)
    {
        return $this->profile->create($data);
    }
}