<?php

namespace Tests\Feature\Repositories;

use App\Contracts\CatalogEntityRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;
use App\Contracts\LabelRepositoryInterface;
use App\Contracts\SongRepositoryInterface;
use App\Contracts\UserRepositoryInterface;

class ArtistRepositoryTest extends RepositoryCrudTestCase
{
    /**
     * @var  $profile  ProfileRepositoryInterface
     */
    protected $profile;

    public function setUp()
    {
        parent::setUp();

        $this->seed('CountriesSeeder');

        $this->profile = resolve(ProfileRepositoryInterface::class);

        $this->album = resolve(AlbumRepositoryInterface::class);

        $this->label = resolve(LabelRepositoryInterface::class);

        $this->user = resolve(UserRepositoryInterface::class);

        $this->catalogEntity = resolve(CatalogEntityRepositoryInterface::class);

        $this->song = resolve(SongRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function setRepository()
    {
        $this->repo = resolve(ArtistRepositoryInterface::class);
    }

    /**
     * Ensure the method create() creates a new record in the database and creates a profile for
     * said Artist.
     *
     * @return void
     */
    public function test_method_create_storesNewResource()
    {
        $profile = factory($this->profile->class())->make()->toArray();

        $artist = $this->repo->create($profile);

        $this->assertInstanceOf($this->repo->class(), $artist);
        $this->assertInstanceOf($this->profile->class(), $artist->profile);
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_updatesResource()
    {
        $profile = factory($this->profile->class())->make(['country_code' => 'US'])->toArray();

        $artist = $this->repo->create($profile);

        $this->repo->update($artist->id, [
            'country_code' => 'CA',
        ]);

        $this->assertTrue(
            $this->repo->findById($artist->id)->profile->country->code === 'CA'
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_returnsModelInstance()
    {
        $profile = factory($this->profile->class())->make(['country_code' => 'US'])->toArray();

        $artist = $this->repo->create($profile);

        $updated = $this->repo->update($artist->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function test_method_delete_deletesResource()
    {
        $profile = factory($this->profile->class())->make(['country_code' => 'US'])->toArray();

        $artist = $this->repo->create($profile);

        factory($this->album->class())->create([
            'artist_id' => $artist->id
        ]);

        $artist->delete();

        $this->assertNull($this->repo->findById($artist->id));
    }

    /**
     * Ensure that an Artist morphs to a CatalogableEntity.
     *
     * @return void
     */
    public function test_catalogable_newArtist_morphsToCatalogableEntity()
    {
        $artist = factory($this->repo->class())->create();

        factory($this->catalogEntity->class())->create([
            'user_id' => factory($this->user->class())->create()->id,
            'catalogable_id' => $artist->id,
            'catalogable_type' => $this->repo->class()
        ]);

        factory($this->profile->class())->create([
            'profilable_id' => $artist->id,
            'profilable_type' => $this->repo->class()
        ]);

        $this->assertInstanceOf(
            $this->catalogEntity->class(),
            $artist->catalogable
        );
    }

    /**
     * Ensure that a new Artist morphs to a Profile.
     *
     * @return void
     */
    public function test_profile_newArtist_morphsToProfile()
    {
        $artist = factory($this->repo->class())->create();

        factory($this->catalogEntity->class())->create([
            'user_id' => factory($this->user->class())->create()->id,
            'catalogable_id' => $artist->id,
            'catalogable_type' => $this->repo->class()
        ]);

        factory($this->profile->class())->create([
            'profilable_id' => $artist->id,
            'profilable_type' => $this->repo->class()
        ]);

        $this->assertInstanceOf(
            $this->profile->class(),
            $artist->profile
        );
    }

    /**
     * Verify that when an Artist is associated with a Label, the Artist belongs
     * to the Label.
     *
     * @return void
     */
    public function test_label_whenAssociatedWithArtist_artistBelongsToLabel()
    {
        $artist = factory($this->repo->class())->state('onLabel')->create();

        $this->assertInstanceOf($this->label->class(), $artist->label);
    }

    /**
     * Verify that when an Album is associated with a new Artist, the Artist has
     * at least one Album.
     *
     * @return void
     */
    public function test_albums_whenAssociatedWithArtist_artistHasManyAlbums()
    {
        $artist = factory($this->repo->class())->create();

        factory($this->album->class())->create(['artist_id' => $artist->id]);

        $this->assertInstanceOf($this->album->class(), $artist->albums()->first());
    }

    // TODO Perhaps this should be changed to test has-many-through.

    /**
     * Verify that when an Album is associated with a new Artist, the Artist has
     * at least one Song (through an Album).
     *
     * @return void
     */
    public function test_songs_whenAlbumAssociatedWithArtist_artistHasManySongs()
    {
        $artist = factory($this->repo->class())->create();

        $album = factory($this->album->class())->create(['artist_id' => $artist->id]);

        factory($this->song->class())->create([
            'track_number' => 1,
            'album_id' => $album->id
        ]);

        $this->assertInstanceOf($this->song->class(), $artist->albums()->first()->songs->first());
    }
}
