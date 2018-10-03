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
     * @var OrderItem $cartItem
     */
    protected $cartItem;

    public function __construct(OrderItem $cartItem)
    {
        $this->cartItem = $cartItem;
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
        return $this->cartItem;
    }
}
