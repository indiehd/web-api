<?php

namespace App\Repositories;

use App\Contracts\GenreRepositoryInterface;
use App\Genre;

class GenreRepository extends CrudRepository implements GenreRepositoryInterface
{
    /**
     * @var string
     */
    protected $class = Genre::class;

    /**
     * @var \App\Genre
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
}
