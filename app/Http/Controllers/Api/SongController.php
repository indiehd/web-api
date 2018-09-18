<?php

namespace App\Http\Controllers\Api;

use App\Contracts\SongRepositoryInterface;
use App\Http\Requests\StoreSong;
use App\Http\Requests\UpdateSong;
use App\Http\Resources\SongResource;

class SongController extends ApiController
{

    /**
     * Should return the <RepositoryInterface>::class
     *
     * @return string
     */
    public function repository()
    {
        return SongRepositoryInterface::class;
    }

    /**
     * Should return the <Resource>::class
     *
     * @return string
     */
    public function resource()
    {
        return SongResource::class;
    }

    /**
     * Should return <StoreRequest>::class
     *
     * @return string
     */
    public function storeRequest()
    {
        return StoreSong::class;
    }

    /**
     * Should return <UpdateRequest>::class
     *
     * @return string
     */
    public function updateRequest()
    {
        return UpdateSong::class;
    }
}
