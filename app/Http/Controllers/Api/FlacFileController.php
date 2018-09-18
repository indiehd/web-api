<?php

namespace App\Http\Controllers\Api;

use App\Contracts\FlacFileRepositoryInterface;
use App\Http\Resources\FlacFileResource;

class FlacFileController extends ApiController
{

    /**
     * Should return the <RepositoryInterface>::class
     *
     * @return string
     */
    public function repository()
    {
        return FlacFileRepositoryInterface::class;
    }

    /**
     * Should return the <Resource>::class
     *
     * @return string
     */
    public function resource()
    {
        return FlacFileResource::class;
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
