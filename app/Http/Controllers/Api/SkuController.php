<?php

namespace App\Http\Controllers\Api;

use App\Contracts\SkuRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\SkuResource;
use Illuminate\Http\Request;

class SkuController extends Controller
{
    /**
     * @var SkuRepositoryInterface
     */
    private $sku;

    /**
     * SkuController constructor.
     *
     * @param SkuRepositoryInterface $sku
     */
    public function __construct(SkuRepositoryInterface $sku)
    {
        $this->sku = $sku;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function all()
    {
        return SkuResource::collection(
            $this->sku->all()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return SkuResource
     */
    public function store(Request $request)
    {
        // TODO: Create StoreSku request
        return new SkuResource($this->sku->create($request->all()));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return SkuResource
     */
    public function show($id)
    {
        return new SkuResource($this->sku->findById($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return SkuResource
     */
    public function update(Request $request, $id)
    {
        // TODO: Create UpdateSku request
        $this->sku->update($id, $request->all());

        return new SkuResource($this->sku->findById($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->sku->delete($id);

        return response(['success' => true], 200);
    }
}
