<?php

namespace Tests\Feature;

use Symfony\Component\VarDumper\Cloner\Data;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use DatabaseSeeder;

use App\CatalogEntity;
use App\Artist;
use App\Profile;
use App\Label;
use App\Album;

class AccountModelTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Ensure that any random Artist morphs to a CatalogableEntity.
     */
    public function test_catalogable_randomArtist_morphsToCatalogableEntity()
    {
        $this->assertInstanceOf(CatalogEntity::class, Artist::inRandomOrder()->first()->catalogable);
    }

    /**
     * Ensure that any random Artist morphs to a Profile.
     */
    public function test_profile_randomArtist_morphsToProfile()
    {
        $this->assertInstanceOf(Profile::class, Artist::inRandomOrder()->first()->profile);
    }

    /**
     * Verify that when an Artist is associated with a Label, the Artist belongs
     * to the Label.
     */
    public function test_label_whenAssociatedWithArtist_artistBelongsToLabel()
    {
        $artist = factory(Artist::class)->state('onLabel')->create();

        $this->assertInstanceOf(Label::class, $artist->label);
    }

    /**
     * Verify that when an Album is associated with an Artist, the Artist has
     * many Albums that include the associated Album.
     */
    public function test_albums_whenAssociatedWithArtist_artistHasManyAlbumsIncludingThisOne()
    {
        $artist = factory(Artist::class)->create();

        $album = factory(Album::class)->create(['artist_id', $artist->id]);

        $this->assertInstanceOf(Album::class, $artist->albums()->first());
    }
}
