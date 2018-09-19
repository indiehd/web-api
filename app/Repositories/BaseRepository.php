<?php

namespace App\Repositories;

use DB;

abstract class BaseRepository
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
        return $this->model()->findOrFail($id);
    }
}
