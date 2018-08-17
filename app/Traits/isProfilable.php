<?php

namespace App\Traits;

use App\Contracts\ProfileRepositoryInterface;

trait isProfilable
{
    public function profilable()
    {
        return resolve(ProfileRepositoryInterface::class);
    }

}