<?php

namespace App\Repositories;

use App\Contracts\OrderRepositoryInterface;
use App\Order;

class OrderRepository extends CrudRepository implements OrderRepositoryInterface
{
    /**
     * @var string $class
     */
    protected $class = Order::class;

    /**
     * @var Order $order
     */
    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
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
     * @return Order
     */
    public function model()
    {
        return $this->order;
    }
}
