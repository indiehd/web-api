<?php

namespace App\Http\Controllers\Api;

use App\Contracts\ArtistRepositoryInterface;
use App\Http\Requests\DestroyArtist;
use App\Http\Requests\StoreArtist;
use App\Http\Requests\UpdateArtist;
use App\Http\Resources\ArtistResource;

class ArtistController extends ApiController
{
    protected $shouldAuthorize = true;

    /**
     * Sets the StoreRequest to resolve for validation during a store request.
     *
     * @return string
     */
    public function storeRequest()
    {
        return StoreArtist::class;
    }

    /**
     * Sets the UpdateRequest to resolve for validation during a update request.
     *
     * @return string
     */
    public function updateRequest()
    {
        return UpdateArtist::class;
    }

    /**
     * Should return <DestroyRequest>::class.
     *
     * @return string
     */
    public function destroyRequest()
    {
        return DestroyArtist::class;
    }

    /**
     * Sets the RepositoryInterface to resolve.
     *
     * @return string
     */
    public function repository()
    {
        return ArtistRepositoryInterface::class;
    }

    /**
     * Sets the ModelResource to resolve.
     *
     * @return string
     */
    public function resource()
    {
        return ArtistResource::class;
    }
}
