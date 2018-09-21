<?php

namespace App\Repositories;

use App\Song;
use App\Contracts\SongRepositoryInterface;
use Tests\Feature\Repositories\SongRepositoryTest;

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

    public function testClass()
    {
        return resolve(SongRepositoryTest::class);
    }
}
