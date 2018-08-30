<?php

namespace App\Repositories;

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
        return $this->findById($id)->delete();
    }
}
