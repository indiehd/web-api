<?php

namespace App\Contracts;

interface AlbumRepositoryInterface extends BaseRepositoryInterface
{
    public function class();
    public function model();
    public function all();
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}