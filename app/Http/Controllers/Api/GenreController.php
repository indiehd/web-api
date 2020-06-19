<?php

namespace App\Http\Controllers\Api;

use App\Contracts\GenreRepositoryInterface;
use App\Http\Requests\StoreGenre;
use App\Http\Requests\UpdateGenre;
use App\Http\Requests\DestroyGenre;
use App\Http\Resources\GenreResource;

class GenreController extends ApiController
{
    protected $shouldAuthorize = true;

    /**
     * Sets the RepositoryInterface to resolve
     *
     * @return string
     */
    public function repository()
    {
        return GenreRepositoryInterface::class;
    }

    /**
     * Sets the ModelResource to resolve
     *
     * @return string
     */
    public function resource()
    {
        return GenreResource::class;
    }

    /**
     * Sets the StoreRequest to resolve for validation during a store request
     *
     * @return string
     */
    public function storeRequest()
    {
        return StoreGenre::class;
    }

    /**
     * Sets the UpdateRequest to resolve for validation during a update request
     *
     * @return string
     */
    public function updateRequest()
    {
        return UpdateGenre::class;
    }

    /**
     * Should return <DestroyRequest>::class
     *
     * @return string
     */
    public function destroyRequest()
    {
        return DestroyGenre::class;
    }
}
