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
use App\Song;

class ArtistModelTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Ensure that any random Artist morphs to a CatalogableEntity.
     *
     * @return void
     */
    public function test_catalogable_randomArtist_morphsToCatalogableEntity()
    {
        $this->assertInstanceOf(CatalogEntity::class, Artist::inRandomOrder()->first()->catalogable);
    }

    /**
     * Ensure that any random Artist morphs to a Profile.
     *
     * @return void
     */
    public function test_profile_randomArtist_morphsToProfile()
    {
        $this->assertInstanceOf(Profile::class, Artist::inRandomOrder()->first()->profile);
    }

    /**
     * Verify that when an Artist is associated with a Label, the Artist belongs
     * to the Label.
     *
     * @return void
     */
    public function test_label_whenAssociatedWithArtist_artistBelongsToLabel()
    {
        $artist = factory(Artist::class)->state('onLabel')->create();

        $this->assertInstanceOf(Label::class, $artist->label);
    }

    /**
     * Verify that when an Album is associated with an Artist, the Artist has
     * many Albums that include the associated Album.
     *
     * @return void
     */
    public function test_albums_whenAssociatedWithArtist_artistHasManyAlbums()
    {
        $artist = factory(Artist::class)->create();

        factory(Album::class)->create(['artist_id' => $artist->id]);

        $this->assertInstanceOf(Album::class, $artist->albums()->first());
    }

    /**
     * Verify that when an Album is associated with an Artist, the Artist has one
     * or more Songs.
     *
     * @return void
     */
    public function test_songs_whenAlbumAssociatedWithArtist_artistHasManySongs()
    {
        $artist = factory(Artist::class)->create();

        $album = factory(Album::class)->create(['artist_id' => $artist->id]);

        $count = rand(1, 10);

        for ($i = 0; $i < $count + 1; $i++) {
            factory(Song::Class)->create([
                'track_number' => $i + 1,
                'album_id' => $album->id
            ]);
        }

        $this->assertFalse($artist->albums()->first()->songs->isEmpty());
    }
}
