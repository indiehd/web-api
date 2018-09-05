<?php

namespace App\Repositories;

use App\Contracts\ProfileRepositoryInterface;
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

    /**
     * Get the model by the given id
     *
     * @param $id
     * @return mixed
     */
    public function findById($id)
    {
        return $this->model()->find($id);
    }

    /**
     * Creates a new model resource and saves it to the database
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->model()->create($data);
    }

    /**
     * Updates a model resource and saves it to the database
     *
     * @param $id
     * @param array $data
     * @return mixed
     */
    public function update($id, array $data)
    {
        $user = $this->findById($id);
        $user->update($data);
        return $user;
    }
}
