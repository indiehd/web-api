<?php

namespace App\Repositories;

use App\Contracts\FlacFileRepositoryInterface;
use App\FlacFile;

class FlacFileRepository extends CrudRepository implements FlacFileRepositoryInterface
{
    /**
     * @var string
     */
    protected $class = FlacFile::class;

    /**
     * @var \App\FlacFile
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
