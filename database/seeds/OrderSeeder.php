<?php

use App\OrderItem;
use App\Album;
use App\Song;

class OrderSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $orderSeedCount = 10;

        $orderItemSeedCount = 10;

        $orderable = [
            App\Song::class,
            App\Album::class
        ];

        for ($i = 1; $i <= $orderSeedCount; $i++) {

            $this->log('Creating an Order');
            $cart = factory(App\Order::class)->create();

            for ($j = 1; $j <= $orderItemSeedCount; $j++) {
                $type = $orderable[rand(0, count($orderable) - 1)];
                $this->log("Creating an Order item for $type");

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

                factory(App\OrderItem::class)->create([
                    'order_id' => $cart->id,
                    'orderable_id' => $entity->id,
                    'orderable_type' => $type
                ]);
            }
        }
    }
}
