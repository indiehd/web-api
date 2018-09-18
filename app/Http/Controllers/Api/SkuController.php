<?php

namespace App\Http\Controllers\Api;

use App\Contracts\SkuRepositoryInterface;
use App\Http\Requests\StoreSku;
use App\Http\Requests\UpdateSku;
use App\Http\Resources\SkuResource;

class SkuController extends ApiController
{

    /**
     * Should return the <RepositoryInterface>::class
     *
     * @return string
     */
    public function repository()
    {
        return SkuRepositoryInterface::class;
    }

    /**
     * Should return the <Resource>::class
     *
     * @return string
     */
    public function resource()
    {
        return SkuResource::class;
    }

    /**
     * Should return <StoreRequest>::class
     *
     * @return string
     */
    public function storeRequest()
    {
        return StoreSku::class;
    }

    /**
     * Should return <UpdateRequest>::class
     *
     * @return string
     */
    public function updateRequest()
    {
        return UpdateSku::class;
    }
}
