<?php

namespace Database\Seeders;

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
