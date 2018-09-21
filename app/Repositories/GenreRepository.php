<?php

namespace App\Repositories;

use App\Genre;
use App\Contracts\GenreRepositoryInterface;
use Tests\Feature\Repositories\GenreRepositoryTest;

class GenreRepository extends CrudRepository implements GenreRepositoryInterface
{
    /**
     * @var string $class
     */
    protected $class = Genre::class;

    /**
     * @var \App\Genre $genre
     */
    protected $genre;

    public function __construct(Genre $genre)
    {
        $this->genre = $genre;
    }

    public function class()
    {
        return $this->class;
    }

    public function model()
    {
        return $this->genre;
    }

    public function testClass()
    {
        return resolve(GenreRepositoryTest::class);
    }
}
