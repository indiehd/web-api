<?php

namespace App\Traits;

use App\Contracts\ProfileRepositoryInterface;

trait IsProfilable
{
    public function profilable()
    {
        return resolve(ProfileRepositoryInterface::class);
    }
}
