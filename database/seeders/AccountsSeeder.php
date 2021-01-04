<?php

namespace Database\Seeders;

use App\Artist;
use App\CatalogEntity;
use App\Label;
use App\Profile;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

function factoryForModel(string $class): Factory
{
    return Factory::factoryForModel($class);
}

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
            Artist::class,
            Label::class,
        ];

        for ($i = 1; $i <= $seed_count; $i++) {

            /*
             * We do this to randomly choose if we are generating
             * a account WITH catalogable entities ELSE we generate a single "Fan Account".
             */
            if ($this->randomBoolean()) {

                /*
                 * If we are generating an account WITH catalogable entities,
                 * we should randomly choose to either generate an account
                 * with BOTH catalogable entities ELSE just 1 single random catalogable entity.
                 */
                if ($this->randomBoolean()) {
                    // users with Artist AND Label Profiles
                    $this->log('Creating a User with Label and Artist Profiles');

                    $user_id = factoryForModel(User::class)->create()->id;

                    foreach ($catalogable as $item) {
                        $entity = factoryForModel($item)->create();

                        factoryForModel(CatalogEntity::class)->create([
                            'user_id' => $user_id,
                            'catalogable_id' => $entity->id,
                            'catalogable_type' => $item,
                        ]);

                        factoryForModel(Profile::class)->create([
                            'profilable_id' => $entity->id,
                            'profilable_type' => $item,
                        ]);
                    }
                } else {
                    // users with Artist OR Label Profile
                    $type = $catalogable[rand(0, count($catalogable) - 1)];
                    $this->log("Creating a User with a $type Profile");

                    $entity = factoryForModel($type)->create();

                    factoryForModel(CatalogEntity::class)->create([
                        'user_id' => factoryForModel(User::class),
                        'catalogable_id' => $entity->id,
                        'catalogable_type' => $type,
                    ]);

                    factoryForModel(Profile::class)->create([
                        'profilable_id' => $entity->id,
                        'profilable_type' => $type,
                    ]);
                }
            } else {
                // users without any Catalogable Entities "Fans"
                $this->log('Creating a User without any catalogable profiles');
                factoryForModel(User::class)->create();
            }
        }
    }

    /**
     * Returns a random boolean.
     *
     * @return bool
     */
    private function randomBoolean()
    {
        return (bool) rand(0, 1);
    }
}
