<?php

namespace App\Http\Controllers\Api;

use App\Contracts\LabelRepositoryInterface;
use App\Http\Requests\StoreLabel;
use App\Http\Requests\UpdateLabel;
use App\Http\Requests\DestroyLabel;
use App\Http\Resources\LabelResource;

class LabelController extends ApiController
{

    /**
     * Sets the RepositoryInterface to resolve
     *
     * @return string
     */
    public function repository()
    {
        return LabelRepositoryInterface::class;
    }

    /**
     * Sets the ModelResource to resolve
     *
     * @return string
     */
    public function resource()
    {
        return LabelResource::class;
    }

    /**
     * Sets the StoreRequest to resolve for validation during a store request
     *
     * @return string
     */
    public function storeRequest()
    {
        return StoreLabel::class;
    }

    /**
     * Sets the UpdateRequest to resolve for validation during a update request
     *
     * @return string
     */
    public function updateRequest()
    {
        return UpdateLabel::class;
    }

    /**
     * Should return <DestroyRequest>::class
     *
     * @return string
     */
    public function destroyRequest()
    {
        return DestroyLabel::class;
    }
}
