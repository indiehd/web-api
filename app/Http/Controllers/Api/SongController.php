<?php

namespace App\Http\Controllers\Api;

use App\Contracts\SongRepositoryInterface;
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
