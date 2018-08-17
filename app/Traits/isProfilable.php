<?php

namespace App\Traits;

use App\Contracts\ProfileRepositoryInterface;

trait isProfilable
{
    public function profile()
    {
        return resolve(ProfileRepositoryInterface::class);
    }

}