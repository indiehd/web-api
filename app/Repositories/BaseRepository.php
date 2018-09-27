<?php

namespace App\Repositories;

abstract class BaseRepository
{
    abstract public function model();

    /**
     * @param int|null $paginate
     * @return mixed
     */
    public function all(int $paginate = null)
    {
        return is_null($paginate)
            ? $this->model()->all()
            : $this->model()->paginate($paginate);
    }

    /**
     * @return object
     * @throws \ReflectionException
     */
    public function new()
    {
        $class = new \ReflectionClass($this->class());

        return $class->newInstance();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findById($id)
    {
        return $this->model()->findOrFail($id);
    }
}
