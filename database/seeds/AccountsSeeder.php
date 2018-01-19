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
        $seed_count = 100;

        $catalogable = [
            App\Artist::class,
            App\Label::class
        ];

        for ($i = 1; $i <= $seed_count; $i++) {

            // add some randomness
            if ([true,false][rand(0,1)]) {

                if ([true,false][rand(0,1)]) {
                    // users with Artist AND Label Profiles
                    $this->log('Creating a User with Label and Artist Profiles');

                    $user_id = factory(App\User::class)->create()->id;

                    foreach ($catalogable as $item) {
                        $entity = factory($item)->create();

                        factory(App\CatalogEntity::class)->create([
                            'user_id' => $user_id,
                            'catalogable_id' => $entity->id,
                            'catalogable_type' => $item
                        ]);

                        factory(App\Profile::class)->create([
                            'profilable_id' => $entity->id,
                            'profilable_type' => $item
                        ]);
                    }

                } else {
                    // users with Artist OR Label Profile
                    $type = $catalogable[rand(0, count($catalogable)-1)];
                    $this->log("Creating a User with a $type Profile");

                    $entity = factory($type)->create();

                    factory(App\CatalogEntity::class)->create([
                        'user_id' => factory(App\User::class)->create()->id,
                        'catalogable_id' => $entity->id,
                        'catalogable_type' => $type
                    ]);

                    factory(App\Profile::class)->create([
                        'profilable_id' => $entity->id,
                        'profilable_type' => $type
                    ]);
                }
            } else {
                // users without any Catalogable Entities "Fans"
                $this->log("Creating a User without any catalogable profiles");
                factory(App\User::class)->create();
            }
        }

    }
}
