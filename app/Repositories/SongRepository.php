<?php

namespace App\Repositories;

use App\Song;
use App\Contracts\SongRepositoryInterface;

class SongRepository extends BaseRepository implements SongRepositoryInterface
{
    /**
     * @var string $class
     */
    protected $class = Song::class;

    /**
     * @var \App\Song $song
     */
    protected $song;

    public function __construct(Song $song)
    {
        $this->song = $song;
    }

    public function class()
    {
        return $this->class;
    }

    public function model()
    {
        return $this->song;
    }

    public function create(array $data)
    {
        return $this->model()->create($data);
    }
}
