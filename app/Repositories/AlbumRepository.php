<?php

namespace App\Repositories;

use DB;
use App\Album;
use App\Contracts\AlbumRepositoryInterface;
use App\Contracts\SongRepositoryInterface;

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

    public function __construct(
        Album $album,
        SongRepositoryInterface $song
    ) {
        $this->album = $album;

        $this->song = $song;
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
        $songs = $data['songs'];

        unset($data['songs']);

        $r = DB::transaction(function () use ($data, $songs) {
            $album = $this->model()->create($data);

            $trackNo = 1;

            foreach ($songs as $song) {
                $song['album_id'] = $album->id;
                $song['track_number'] = $trackNo;

                $s = $this->song->model()->create($song);

                $s->album()->associate($album);

                $trackNo++;
            }

            $album->save();

            return $album;
        });

        return $r;
    }
}
