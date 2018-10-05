<?php

namespace Tests\Feature\Repositories;

use App\Contracts\CatalogEntityRepositoryInterface;
use App\Contracts\AccountRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;
use App\Contracts\LabelRepositoryInterface;
use App\Contracts\SongRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ArtistRepositoryTest extends RepositoryCrudTestCase
{
    /**
     * @var $account AccountRepositoryInterface
     */
    protected $account;

    /**
     * @var $profile ProfileRepositoryInterface
     */
    protected $profile;

    /**
     * @var $album AlbumRepositoryInterface
     */
    protected $album;

    /**
     * @var $label LabelRepositoryInterface
     */
    protected $label;

    /**
     * @var $user UserRepositoryInterface
     */
    protected $user;

    /**
     * @var $catalogEntity CatalogEntityRepositoryInterface
     */
    protected $catalogEntity;

    /**
     * @var $song SongRepositoryInterface
     */
    protected $song;

    public function setUp()
    {
        parent::setUp();

        $this->seed('CountriesSeeder');

        $this->account = resolve(AccountRepositoryInterface::class);

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
     * Make a new User object.
     *
     * @param array $userProperties
     * @param array $accountProperties
     * @return \App\User
     */
    public function makeUser(array $userProperties = [], array $accountProperties = [])
    {
        $user = factory($this->user->class())->make($userProperties);

        $account = factory($this->account->class())->make($accountProperties);

        $user = $this->user->create([
            'username' => $user->username,
            'password' => $user->password,
            'account' => $account->toArray(),
        ]);

        return $user;
    }

    /**
     * Ensure the method create() creates a new record in the database and creates a profile for
     * said Artist.
     *
     * @return void
     */
    public function test_method_create_storesNewResource()
    {
        $profile = factory($this->profile->class())->make();

        $artist = $this->repo->create($profile->toArray());

        $this->assertInstanceOf($this->repo->class(), $artist);
        $this->assertInstanceOf($this->profile->class(), $artist->profile);
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_updatesResource()
    {
        $profile = factory($this->profile->class())->make(['country_code' => 'US']);

        $artist = $this->repo->create($profile->toArray());

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
        $profile = factory($this->profile->class())->make();

        $artist = $this->repo->create($profile->toArray());

        $updated = $this->repo->update($artist->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function test_method_delete_deletesResource()
    {
        $profile = factory($this->profile->class())->make();

        $artist = $this->repo->create($profile->toArray());

        $artist->delete();

        try {
            $this->repo->findById($artist->id);
        } catch(ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Ensure that an Artist morphs to a CatalogableEntity.
     *
     * @return void
     */
    public function test_catalogable_newArtist_morphsToCatalogableEntity()
    {
        $artist = $this->repo->create(
            factory($this->profile->class())->raw()
        );

        $user = $this->makeUser();

        $catalogEntity = factory($this->catalogEntity->class())->make([
            'user_id' => $user->id,
            'catalogable_id' => $artist->id,
            'catalogable_type' => $this->repo->class()
        ]);

        $this->catalogEntity->create($catalogEntity->toArray());

        $profile = factory($this->profile->class())->make([
            'profilable_id' => $artist->id,
            'profilable_type' => $this->repo->class()
        ]);

        $this->profile->create($profile->toArray());

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
        $profile = factory($this->profile->class())->make();

        $artist = factory($this->repo->class())->make(
            $profile->toArray()
        );

        $artist = $this->repo->create($artist->toArray());

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
        $labelProfile = factory($this->profile->class())->make();

        $label = $this->label->create($labelProfile->toArray());

        $artistProfile = factory($this->profile->class())->make();

        $artist = factory($this->repo->class())->make(
            array_merge($artistProfile->toArray(), ['label_id' => $label->id])
        );

        $artist = $this->repo->create($artist->toArray());

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
        $artist = $this->repo->create(
            factory($this->profile->class())->raw()
        );

        $this->album->create(
            factory($this->album->class())->raw(['artist_id' => $artist->id])
        );

        $this->assertInstanceOf($this->album->class(), $artist->albums()->first());
    }

    /**
     * Verify that when an Album is associated with a new Artist, the Artist has
     * at least one Song (through an Album).
     *
     * @return void
     */
    public function test_songs_whenAlbumAssociatedWithArtist_artistHasManySongs()
    {
        $artist = $this->repo->create(
            factory($this->profile->class())->raw()
        );

        $album = $this->album->create(
            factory($this->album->class())->raw(['artist_id' => $artist->id])
        );

        $song = factory($this->song->class())->make([
            'track_number' => 1,
            'album_id' => $album->id
        ]);

        $this->song->create($song->toArray());

        $this->assertInstanceOf($this->song->class(), $artist->albums()->first()->songs->first());
    }
}
