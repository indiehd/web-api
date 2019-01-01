<?php

namespace App\Http\Controllers\Api;

use App\Contracts\OrderItemRepositoryInterface;
use App\Http\Requests\StoreOrderItem;
use App\Http\Requests\UpdateOrderItem;
use App\Http\Resources\OrderItemResource;

class OrderItemController extends ApiController
{
    /**
     * Should return the <RepositoryInterface>::class
     *
     * @return string
     */
    public function repository()
    {
        return OrderItemRepositoryInterface::class;
    }

    /**
     * Should return the <Resource>::class
     *
     * @return string
     */
    public function resource()
    {
        return OrderItemResource::class;
    }

    /**
     * Should return <StoreRequest>::class
     *
     * @return string
     */
    public function storeRequest()
    {
        return StoreOrderItem::class;
    }

    /**
     * Should return <UpdateRequest>::class
     *
     * @return string
     */
    public function updateRequest()
    {
        return UpdateOrderItem::class;
    }
}
