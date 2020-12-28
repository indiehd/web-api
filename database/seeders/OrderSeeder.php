<?php

namespace Database\Seeders;

use Database\Factories\OrderFactory;
use Illuminate\Database\Seeder;
use IndieHD\Velkart\Models\Eloquent\Cart;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $carts = Cart::inRandomOrder()->take(rand(10, 20))->get();

        // TODO @cbj4074 how do we hook up the cart now that the order
        // no longer has a cart_id?

        foreach ($carts as $cart) {
            OrderFactory::new()->create()
                ->each(function ($order) use ($cart) {
                    // $products = unserialize($cart->content);

                    // TODO price error
                    // SQLSTATE[HY000]: General error: 1364 Field 'price' doesn't have a default value (SQL: insert into `order_product` (`order_id`, `product_id`) values (1, 96))
                    // $order->products()->saveMany($products);
                });
        }
    }
}
