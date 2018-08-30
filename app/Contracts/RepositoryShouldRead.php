<?php

namespace App\Contracts;

interface RepositoryShouldRead
{
    public function class();

    public function new();

    public function model();

    public function all();

    public function findById($id);
}
