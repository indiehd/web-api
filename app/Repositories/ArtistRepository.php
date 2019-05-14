<?php

namespace App\Repositories;

use App\Artist;
use App\Contracts\ArtistRepositoryInterface;
use App\Traits\IsProfilable;

class ArtistRepository extends CrudRepository implements ArtistRepositoryInterface
{
    use IsProfilable;

    /**
     * @var string $class
     */
    protected $class = Artist::class;

    /**
     * @var Artist
     */
    protected $artist;

    public function __construct(Artist $artist)
    {
        $this->artist = $artist;
    }

    public function class()
    {
        return $this->class;
    }

    public function model()
    {
        return $this->artist;
    }

    public function create(array $data)
    {
        $model = $this->model()->create([
            'label_id' => $data['label_id'] ?? null
        ]);

        $this->createProfile(
            $model,
            $data['moniker'],
            $data['city'] ?? null,
            $data['territory'] ?? null,
            $data['country_code'] ?? null,
            $data['profile_url']
        );

        return $model;
    }

    public function update($id, array $data)
    {
        $model = $this->findById($id);

        $this->updateProfile($model->profile->id, $data);

        return $model;
    }

    public function profile()
    {
        return $this->artist->profile();
    }

    public function featurable()
    {
        return $this->model()->featurable();
    }
}
