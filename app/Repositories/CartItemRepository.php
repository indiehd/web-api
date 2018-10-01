<?php

namespace App\Repositories;

use App\Contracts\CartItemRepositoryInterface;
use App\CartItem;

class CartItemRepository extends CrudRepository implements CartItemRepositoryInterface
{
    /**
     * @var string $class
     */
    protected $class = CartItem::class;

    /**
     * @var CartItem $cartItem
     */
    protected $cartItem;

    public function __construct(CartItem $cartItem)
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
     * @return CartItem
     */
    public function model()
    {
        return $this->cartItem;
    }
}
