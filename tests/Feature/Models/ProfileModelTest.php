<?php

namespace Tests\Feature\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use DB;
use DatabaseSeeder;
use App\Profile;

class ProfileModelTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Ensure that models of every Profilable type in the database morph
     * to a Profile.
     *
     * @return void
     */
    public function test_profilable_allDistinctTypes_morphToProfile()
    {
        $profilableTypes = DB::table('profiles')
            ->select('profilable_type')
            ->distinct('profilable_type')
            ->groupBy('profilable_type')
            ->get();

        foreach ($profilableTypes as $type) {
            $randomModelOfType = (new $type->profilable_type)::inRandomOrder()->first();

            $this->assertInstanceOf(Profile::class, $randomModelOfType->profile);
        }
    }
}
