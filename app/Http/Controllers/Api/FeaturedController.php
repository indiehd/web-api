<?php

namespace App\Http\Controllers\Api;

use App\Contracts\FeaturedRepositoryInterface;
use App\Http\Requests\StoreFeatured;
use App\Http\Requests\UpdateFeatured;
use App\Http\Requests\DestroyFeatured;
use App\Http\Resources\FeaturedResource;
use App\Http\Resources\ArtistResource;

class FeaturedController extends ApiController
{
    public function __construct(
        FeaturedRepositoryInterface $featured
    ) {
        parent::__construct();

        $this->featured = $featured;
    }

    /**
     * Sets the StoreRequest to resolve for validation during a store request
     *
     * @return string
     */
    public function storeRequest()
    {
        return StoreFeatured::class;
    }

    /**
     * Sets the UpdateRequest to resolve for validation during a update request
     *
     * @return string
     */
    public function updateRequest()
    {
        return UpdateFeatured::class;
    }

    /**
     * Should return <DestroyRequest>::class
     *
     * @return string
     */
    public function destroyRequest()
    {
        return DestroyFeatured::class;
    }

    /**
     * Sets the RepositoryInterface to resolve
     *
     * @return string
     */
    public function repository()
    {
        return FeaturedRepositoryInterface::class;
    }

    /**
     * Sets the ModelResource to resolve
     *
     * @return string
     */
    public function resource()
    {
        return FeaturedResource::class;
    }

    /**
     * Returns a Featured Artist resource.
     *
     * @return string
     */
    public function artists()
    {
        return ArtistResource::collection(
            $this->featured
                ->artists()
                ->with('featurable')
                ->get()
                ->pluck('featurable')
        );
    }
}
