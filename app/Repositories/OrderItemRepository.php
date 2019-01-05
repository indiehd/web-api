<?php

namespace App\Repositories;

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
}
