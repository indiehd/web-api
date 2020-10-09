<?php

namespace Database\Seeders;

class CatalogSeeder extends BaseSeeder
{
    /**
     * Seed the database tables that comprise the Digital Catalog.
     *
     * @return void
     */
    public function run()
    {
        $this->call(StaticDataSeeder::class);

        $this->call(AccountsSeeder::class);

        $this->call(AlbumsSeeder::class);
    }
}
