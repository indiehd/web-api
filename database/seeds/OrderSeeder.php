<?php

use Illuminate\Database\Seeder;
use IndieHD\Velkart\Models\Eloquent\Cart;
use IndieHD\Velkart\Models\Eloquent\Order;

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
            factory(Order::class)->create([
                'cart_id' => $cart->id,
            ])
                ->each(function ($order) use ($cart) {
                    $products = unserialize($cart->content);

                    $order->products()->saveMany($products);
                });
        }
    }
}
