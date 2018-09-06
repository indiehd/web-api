<?php

namespace App\Repositories;

use DB;
use App\Contracts\RepositoryShouldRead;

abstract class BaseRepository implements RepositoryShouldRead
{
    public function all()
    {
        return $this->model()->all();
    }

    public function new()
    {
        $class = new \ReflectionClass($this->class());

        return $class->newInstance();
    }

    public function findById($id)
    {
        return $this->model()->find($id);
    }

    public function delete($id)
    {
        DB::transaction(function () use ($id) {
            return $this->findById($id)->delete();
        });
    }

    public function update($id, array $data)
    {
        $model = $this->findById($id);

        $model->update($data);

        return $model;
    }
}
