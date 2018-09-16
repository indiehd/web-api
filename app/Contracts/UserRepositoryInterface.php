<?php

namespace App\Contracts;

interface UserRepositoryInterface extends RepositoryShouldCrud
{
    public function songs();

    public function purchasedSongs();
}
