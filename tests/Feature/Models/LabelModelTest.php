<?php

namespace Tests\Feature\Models;

use Symfony\Component\VarDumper\Cloner\Data;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use DatabaseSeeder;

use App\CatalogEntity;
use App\Artist;
use App\Profile;
use App\Label;
use App\Album;

class LabelModelTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Ensure that any random Label morphs to a CatalogableEntity.
     *
     * @return void
     */
    public function test_catalogable_randomLabel_morphsToCatalogableEntity()
    {
        $this->assertInstanceOf(CatalogEntity::class, Label::inRandomOrder()->first()->catalogable);
    }

    /**
     * Ensure that any random Label morphs to a Profile.
     *
     * @return void
     */
    public function test_profile_randomLabel_morphsToProfile()
    {
        $this->assertInstanceOf(Profile::class, Label::inRandomOrder()->first()->profile);
    }

    /**
     * Verify that when an Artist is associated with a Label, the Label has
     * many Artists.
     *
     * @return void
     */
    public function test_artists_whenAssociatedWithLabel_labelHasManyArtists()
    {
        $artist = factory(Artist::class)->state('onLabel')->create();

        $this->assertInstanceOf(Artist::class, $artist->label->artists->first());
    }

    /**
     * Verify that when an Album is associated with an Artist that is associated
     * with a Label, the Label has many Albums.
     *
     * @return void
     */
    public function test_albums_whenAssociatedWithLabelThroughArtist_labelHasManyAlbums()
    {
        $artist = factory(Artist::class)->state('onLabel')->create();

        factory(Album::class)->create(['artist_id' => $artist->id]);

        $this->assertInstanceOf(Album::class, $artist->label->albums->first());
    }
}
