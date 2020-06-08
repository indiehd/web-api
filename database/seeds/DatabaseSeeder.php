<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(StaticDataSeeder::class);

        $this->call(AccountsSeeder::class);

        $this->call(AlbumsSeeder::class);

        $this->call(OrderSeeder::class);

        $this->call(FeaturedSeeder::class);
    }
}
