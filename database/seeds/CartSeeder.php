<?php

use App\CartItem;
use App\Album;
use App\Song;

class CartSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cartSeedCount = 10;

        $cartItemSeedCount = 10;

        $cartable = [
            App\Song::class,
            App\Album::class
        ];

        for ($i = 1; $i <= $cartSeedCount; $i++) {

            $this->log('Creating a Cart instance');
            $cart = factory(App\Cart::class)->create();

            for ($j = 1; $j <= $cartItemSeedCount; $j++) {
                $type = $cartable[rand(0, count($cartable) - 1)];
                $this->log("Creating a Cart item for $type");

                if ($type === 'App\Album') {
                    $entity = factory(Album::class)->create();
                } elseif ($type === 'App\Song') {
                    $album = factory(Album::class)->create();

                    $song = factory(Song::class)->create([
                        'album_id' => $album->id,
                        'track_number' => 1,
                    ]);

                    $entity = $song;
                }

                factory(App\CartItem::class)->create([
                    'cart_id' => $cart->id,
                    'cartable_id' => $entity->id,
                    'cartable_type' => $type
                ]);
            }
        }
    }
}
