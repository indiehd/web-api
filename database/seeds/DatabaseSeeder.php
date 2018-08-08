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
        $this->call(CountriesSeeder::class);

        $this->call(GenresSeeder::class);

        $this->call(AccountsSeeder::class);
    }
}
