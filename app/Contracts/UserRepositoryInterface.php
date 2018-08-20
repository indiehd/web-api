<?php

namespace App\Contracts;

interface UserRepositoryInterface
{
    public function songs();

    public function purchasedSongs();
}
