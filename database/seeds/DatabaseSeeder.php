<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    private $types = [
        App\CatalogEntity::class,
        App\Fan::class
    ];

    private $catalogable = [
        App\Artist::class,
        App\Label::class
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedAccounts(10);
    }

    private function seedAccounts($amount = 5)
    {
        for ($i = 1; $i <= $amount; $i++) {
            $catalogable_type = $this->catalogable[rand(0, count($this->catalogable)-1)];

            $type = app($this->types[rand(0, count($this->types)-1)]);

            if ($type instanceof App\CatalogEntity) {

                $this->log('Creating A CatalogEntity');
                $this->log("CatalogEntity Type is: $catalogable_type");

                $type::create([
                    'user_id' => factory(App\User::class)->create()->id,
                    'catalogable_id' => factory($catalogable_type)->create()->id,
                    'catalogable_type' => $catalogable_type
                ]);
            } else {

                $this->log('Creating A Fan');

                $type::create([
                    'user_id' => factory(App\User::class)->create()->id
                ]);
            }
        }
    }

    private function log($msg)
    {
        return $this->command->getOutput()->writeln("<info>LOG:</info> $msg");
    }
}
