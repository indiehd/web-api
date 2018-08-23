<?php

namespace App\Repositories;

use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\RepositoryShouldCrud;
use App\Profile;

class ProfileRepository extends BaseRepository implements ProfileRepositoryInterface
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

    public function model()
    {
        return $this->profile;
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
        return $this->findById($id)->update($data);
    }
}