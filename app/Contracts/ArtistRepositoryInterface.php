<?php

namespace App\Contracts;

interface ArtistRepositoryInterface extends RepositoryShouldCrud
{
    public function profile();
}
