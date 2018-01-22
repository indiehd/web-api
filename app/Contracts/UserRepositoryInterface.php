<?php

namespace App\Contracts;

interface UserRepositoryInterface
{
    public function all();
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function songs();
    public function purchasedSongs();
}