<?php

namespace App\Repositories;

use App\Contracts\SongRepositoryInterface;
use App\Song;

class SongRepository extends CrudRepository implements SongRepositoryInterface
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

    /**
     * @inheritDoc
     */
    public function all()
    {
        return $this->model()
            ->whereHas('album', function ($q) {
                $q->where('is_active', 1);
            })
            ->where('is_active', 1)
            ->get();
    }
}
