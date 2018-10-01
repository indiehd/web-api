<?php

namespace App\Repositories;

use App\Contracts\CartRepositoryInterface;
use App\Cart;

class CartRepository extends CrudRepository implements CartRepositoryInterface
{
    /**
     * @var string $class
     */
    protected $class = Cart::class;

    /**
     * @var Cart $cart
     */
    protected $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
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
        return $this->cart;
    }
}
