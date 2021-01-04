<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use IndieHD\Velkart\Database\Factories\OrderFactory;
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

        foreach ($carts as $cart) {
            OrderFactory::new()->create()
                ->each(function ($order) use ($cart) {
                    $products = unserialize($cart->content);

                    $order->products()->saveMany($products);
                });
        }
    }
}
