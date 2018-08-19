<?php

namespace App\Contracts;

interface ProfileRepositoryInterface
{
    public function model();

    public function findById($id);

    public function create(array $data);

    public function update($id, array $data);
}
