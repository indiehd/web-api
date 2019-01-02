<?php

namespace App\Http\Controllers\Api;

use App\Contracts\OrderRepositoryInterface;
use App\Http\Requests\StoreOrder;
use App\Http\Requests\UpdateOrder;
use App\Http\Resources\OrderResource;

class OrderController extends ApiController
{
    /**
     * Should return the <RepositoryInterface>::class
     *
     * @return string
     */
    public function repository()
    {
        return OrderRepositoryInterface::class;
    }

    /**
     * Should return the <Resource>::class
     *
     * @return string
     */
    public function resource()
    {
        return OrderResource::class;
    }

    /**
     * Should return <StoreRequest>::class
     *
     * @return string
     */
    public function storeRequest()
    {
        return StoreOrder::class;
    }

    /**
     * Should return <UpdateRequest>::class
     *
     * @return string
     */
    public function updateRequest()
    {
        return UpdateOrder::class;
    }
}
