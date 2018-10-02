<?php

class DatabaseSeeder extends BaseSeeder
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

        $this->call(CartSeeder::class);
    }
}
