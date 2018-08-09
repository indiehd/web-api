<?php

use Illuminate\Database\Seeder;

class AlbumsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Album::class, 50)->create()->each(function ($album) {
            for ($i = 1; $i < rand(2, 20); $i++) {
                $album->songs()->save(factory(App\Song::class)->make([
                    'album_id' => $album->id,
                    'track_number' => $i,
                ]));
            }
        });
    }
}
