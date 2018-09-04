<?php

namespace Tests\Feature\Models;

use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\LabelRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use CountriesSeeder;
use App\CatalogEntity;

class ProfileModelTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->seed(CountriesSeeder::class);

        $this->profile = resolve(ProfileRepositoryInterface::class);

        $this->artist = resolve(ArtistRepositoryInterface::class);

        $this->label = resolve(LabelRepositoryInterface::class);

        $this->user = resolve(UserRepositoryInterface::class);
    }

    /**
     * Ensure that models of every Profilable type in the database morph
     * to a Profile.
     *
     * @return void
     */
    public function test_profilable_allDistinctTypes_morphToProfile()
    {
        $artist = factory($this->artist->class())->create();

        factory(CatalogEntity::class)->create([
            'user_id' => factory($this->user->class())->create()->id,
            'catalogable_id' => $artist->id,
            'catalogable_type' => $this->artist->class()
        ]);

        factory($this->profile->class())->create([
            'profilable_id' => $artist->id,
            'profilable_type' => $this->artist->class()
        ]);

        $this->assertInstanceOf($this->profile->class(), $artist->profile);

        $label = factory($this->label->class())->create();

        factory(CatalogEntity::class)->create([
            'user_id' => factory($this->user->class())->create()->id,
            'catalogable_id' => $label->id,
            'catalogable_type' => $this->label->class()
        ]);

        factory($this->profile->class())->create([
            'profilable_id' => $label->id,
            'profilable_type' => $this->label->class()
        ]);

        $this->assertInstanceOf($this->profile->class(), $label->profile);
    }
}
