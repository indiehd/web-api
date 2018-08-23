<?php

namespace App\Repositories;

use App\FlacFile;
use App\Contracts\FlacFileRepositoryInterface;

class FlacFileRepository extends BaseRepository implements FlacFileRepositoryInterface
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

    public function create(array $data)
    {
        return $this->model()->create($data);
    }

    public function update($id, array $data)
    {
        return $this->model()->find($id)->update($data);
    }
}
