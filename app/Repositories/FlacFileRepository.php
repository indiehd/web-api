<?php

namespace App\Repositories;

use App\FlacFile;
use App\Contracts\FlacFileRepositoryInterface;

class FlacFileRepository extends CrudRepository implements FlacFileRepositoryInterface
{
    /**
     * @var string $class
     */
    protected $class = FlacFile::class;

    /**
     * @var \App\FlacFile $flacFile
     */
    protected $flacFile;

    public function __construct(FlacFile $flacFile)
    {
        $this->flacFile = $flacFile;
    }

    public function class()
    {
        return $this->class;
    }

    public function model()
    {
        return $this->flacFile;
    }
}
