<?php

namespace App\Contracts;

interface RepositoryShouldCrud extends RepositoryShouldRead
{
    public function create(array $data);

    public function update($id, array $data);

    public function delete($id);
}
