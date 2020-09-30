<?php

class CommerceSeeder extends BaseSeeder
{
    /**
     * Seed all e-commerce-related data.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CartSeeder::class);

        $this->call(OrderSeeder::class);
    }
}
