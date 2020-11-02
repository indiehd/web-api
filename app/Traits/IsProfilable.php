<?php

namespace App\Traits;

use App\Contracts\ProfileRepositoryInterface;

trait IsProfilable
{
    public function createProfile(
        $model,
        $moniker,
        $city,
        $territory,
        $country_code,
        $profile_url
    ) {
        $this->profilable()->create([
            'moniker' => $moniker,
            'city' => $city,
            'territory' => $territory,
            'country_code' => $country_code,
            'profile_url' => $profile_url,
            'profilable_id' => $model->id,
            'profilable_type' => $this->class,
        ]);
    }

    public function updateProfile($id, array $data)
    {
        $this->profilable()->update($id, $data);
    }

    protected function profilable()
    {
        return resolve(ProfileRepositoryInterface::class);
    }
}
