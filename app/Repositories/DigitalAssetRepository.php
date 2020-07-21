<?php

namespace App\Repositories;

use App\Contracts\DigitalAssetRepositoryInterface;
use App\DigitalAsset;
use Illuminate\Database\QueryException;

class DigitalAssetRepository extends CrudRepository implements DigitalAssetRepositoryInterface
{
    /**
     * @var string $class
     */
    protected $class = DigitalAsset::class;

    /**
     * @var DigitalAsset $digitalAsset
     */
    protected $digitalAsset;

    public function __construct(DigitalAsset $digitalAsset)
    {
        $this->digitalAsset = $digitalAsset;
    }

    /**
     * Returns the class namespace.
     *
     * @return string
     */
    public function class()
    {
        return $this->class;
    }

    /**
     * Returns the Repository's Model instance.
     *
     * @return DigitalAsset
     */
    public function model()
    {
        return $this->digitalAsset;
    }

    public function create(array $data)
    {
        #try {
            return $this->model()->create($data);
        #} catch (QueryException $e) {
        #    // If Integrity Constraint violation, the same item has already
        #    // been added to the Order, and we should simply ignore the failure.

        #    if ($e->getCode() !== '23000') {
        #        throw $e;
        #    }
        #}
    }

    public function findByIds($productId, $assetId, $assetType)
    {
        return $this->model()->where([
            'product_id' => $productId,
            'asset_id' => $assetId,
            'asset_type' => $assetType
        ])->first();
    }
}
