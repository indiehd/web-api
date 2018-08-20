<?php

namespace App\Repositories;

use App\Contracts\BaseRepositoryInterface;

abstract class BaseRepository implements BaseRepositoryInterface
{
    public function all()
    {
        return $this->model()->all();
    }

    public function findById($id)
    {
        return $this->model()->find($id);
    }

    public function create(array $data)
    {
        $model = $this->model()->create([]);

        return $model;
    }

    public function update($id, array $data)
    {
        return $this->findById($id);
    }

    public function delete($id)
    {
        return $this->findById($id)->delete();
    }
}
