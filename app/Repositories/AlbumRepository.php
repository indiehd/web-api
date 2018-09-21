<?php

namespace App\Repositories;

use App\Album;
use App\Contracts\AlbumRepositoryInterface;
use Tests\Feature\Repositories\AlbumRepositoryTest;

class AlbumRepository extends CrudRepository implements AlbumRepositoryInterface
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

    public function testClass()
    {
        return resolve(AlbumRepositoryTest::class);
    }
}
