<?php

use IndieHD\Velkart\Models\Eloquent\Cart;
use IndieHD\Velkart\Models\Eloquent\Product;

class CartSeeder extends BaseSeeder
{
    /**
     * Seed carts.
     *
     * @return void
     */
    public function run()
    {
        factory(Cart::class, 20)->create()
            ->each(function ($cart) {
                $cart->content = serialize(Product::inRandomOrder()->take(rand(1, 10))->get());

                $cart->save();
            });
    }
}
