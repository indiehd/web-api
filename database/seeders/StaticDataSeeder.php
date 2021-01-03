<?php

namespace Database\Seeders;

use IndieHD\Velkart\Database\Seeders\CountriesSeeder;

class StaticDataSeeder extends BaseSeeder
{
    /**
     * Seed the database tables that do not change due to end-user activity.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CountriesSeeder::class);

        $this->call(GenresSeeder::class);
    }
}
