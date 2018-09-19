<?php

namespace App\Repositories;

abstract class CrudRepository extends BaseRepository
{
    public function create(array $data)
    {
        return $this->model()->create($data);
    }

    public function update($id, array $data)
    {
        $model = $this->findById($id);
        $model->update($data);
        return $model;
    }

    public function delete($id)
    {
        DB::transaction(function () use ($id) {
            return $this->findById($id)->delete();
        });
    }
}
