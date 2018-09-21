<?php

namespace App\Repositories;

abstract class BaseRepository
{
    abstract public function model();

    abstract public function testClass();

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
