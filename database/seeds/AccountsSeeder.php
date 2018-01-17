<?php

class AccountsSeeder extends BaseSeeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $seed_count = 50;

        $types = [
            App\CatalogEntity::class,
            App\Fan::class
        ];

        $catalogable = [
            App\Artist::class,
            App\Label::class
        ];

        for ($i = 1; $i <= $seed_count; $i++) {
            $catalogable_type = $catalogable[rand(0, count($catalogable)-1)];

            $type = $types[rand(0, count($types)-1)];

            if ($type === App\CatalogEntity::class) {

                $this->log('Creating A CatalogEntity');
                $this->log("CatalogEntity Type is: $catalogable_type");

                factory($type)->create([
                    'catalogable_id' => factory($catalogable_type)->create()->id,
                    'catalogable_type' => $catalogable_type
                ]);

            } else {

                $this->log('Creating A Fan');

                factory($type)->create();
            }
        }

    }
}
