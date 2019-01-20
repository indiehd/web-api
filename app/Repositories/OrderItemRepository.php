<?php

namespace App\Repositories;

use Illuminate\Database\QueryException;
use App\Contracts\OrderItemRepositoryInterface;
use App\OrderItem;

class OrderItemRepository extends CrudRepository implements OrderItemRepositoryInterface
{
    /**
     * @var string $class
     */
    protected $class = OrderItem::class;

    /**
     * @var OrderItem $orderItem
     */
    protected $orderItem;

    public function __construct(OrderItem $orderItem)
    {
        $this->orderItem = $orderItem;
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
     * @return OrderItem
     */
    public function model()
    {
        return $this->orderItem;
    }

    public function create(array $data)
    {
        try {
            return $this->model()->create($data);
        } catch (QueryException $e) {
            // If Integrity Constraint violation, the same item has already
            // been added to the Order, and we should simply ignore the failure.

            if ($e->getCode() !== '23000') {
                throw $e;
            }
        }
    }

    public function findByOrderId($orderId, $orderableId, $orderableType)
    {
        return $this->model()->where([
            'order_id' => $orderId,
            'orderable_id' => $orderableId,
            'orderable_type' => $orderableType
        ])->first();
    }
}
