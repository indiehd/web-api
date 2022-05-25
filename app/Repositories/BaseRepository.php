<?php

namespace App\Repositories;

use App\Contracts\RepositoryShouldRead;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements RepositoryShouldRead
{
    /**
     * @return mixed
     */
    public function all()
    {
        return $this->model()->all();
    }

    /**
     * @param  int  $limit
     * @param  int|null  $paginate
     * @return mixed
     */
    public function limit(int $limit, int $paginate = null)
    {
        $results = $this->model()->limit($limit)->get();

        if ($paginate) {
            $page = LengthAwarePaginator::resolveCurrentPage();
            $items = $results->slice(($page * $paginate) - $paginate, $paginate)->all();

            return new LengthAwarePaginator($items, count($results), $paginate);
        }

        return $results;
    }

    /**
     * @param  int  $paginate
     * @return mixed
     */
    public function paginate(int $paginate)
    {
        return $this->model()->paginate($paginate);
    }

    /**
     * @return object
     *
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
