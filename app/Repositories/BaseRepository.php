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

    abstract public function create(array $data);

    abstract public function update($id, array $data);

    public function delete($id)
    {
        return $this->findById($id)->delete();
    }
}
