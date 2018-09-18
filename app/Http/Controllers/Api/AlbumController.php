<?php

namespace App\Http\Controllers\Api;

use App\Contracts\AlbumRepositoryInterface;
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
        // TODO: Implement storeRequest() method.
    }

    /**
     * Should return <UpdateRequest>::class
     *
     * @return string
     */
    public function updateRequest()
    {
        // TODO: Implement updateRequest() method.
    }
}
