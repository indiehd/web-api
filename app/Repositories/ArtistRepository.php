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
        $model = $this->model()->create([]);

        $this->createProfile(
            $model,
            $data['moniker'],
            $data['city'],
            $data['territory'],
            $data['country_code'],
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
        $this->artist->profile();
    }
}
