<?php

namespace App\Http\Controllers\Api;

use App\Contracts\AlbumRepositoryInterface;
use App\Http\Requests\StoreAlbum;
use App\Http\Requests\UpdateAlbum;
use App\Http\Requests\DestroyAlbum;
use App\Http\Resources\AlbumResource;

class AlbumController extends ApiController
{

    /**
     * Should return the <RepositoryInterface>::class
     *
     * @return string
     */
    public function repository()
    {
        return AlbumRepositoryInterface::class;
    }

    /**
     * Should return the <Resource>::class
     *
     * @return string
     */
    public function resource()
    {
        return AlbumResource::class;
    }

    /**
     * Should return <StoreRequest>::class
     *
     * @return string
     */
    public function storeRequest()
    {
        return StoreAlbum::class;
    }

    /**
     * Should return <UpdateRequest>::class
     *
     * @return string
     */
    public function updateRequest()
    {
        return UpdateAlbum::class;
    }

    /**
     * Should return <DestroyRequest>::class
     *
     * @return string
     */
    public function destroyRequest()
    {
        return DestroyAlbum::class;
    }
}
