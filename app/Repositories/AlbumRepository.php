<?php

namespace App\Repositories;

use App\Album;
use App\Contracts\AlbumRepositoryInterface;

class AlbumRepository extends BaseRepository implements AlbumRepositoryInterface
{
    /**
     * @var string $class
     */
    protected $class = Album::class;

    /**
     * @var Album
     */
    protected $album;

    public function __construct(Album $album)
    {
        $this->album = $album;
    }

    public function class()
    {
        return $this->class;
    }

    public function model()
    {
        return $this->album;
    }

    public function create(array $data)
    {
        return $this->model()->create($data);
    }

    public function update($id, array $data)
    {
        return $this->model()->find($id)->update($data);
    }
}
