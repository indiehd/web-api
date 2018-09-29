<?php

namespace Tests\Feature\Repositories;

use App\Contracts\AccountRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\LabelRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\CatalogEntityRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LabelRepositoryTest extends RepositoryCrudTestCase
{
    /**
     * @var AccountRepositoryInterface $account
     */
    protected $account;

    /**
     * @var ProfileRepositoryInterface $profile
     */
    protected $profile;

    /**
     * @var CatalogEntityRepositoryInterface $catalogEntity
     */
    protected $catalogEntity;

    /**
     * @var UserRepositoryInterface $user
     */
    protected $user;

    /**
     * @var AlbumRepositoryInterface $album
     */
    protected $album;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        $this->seed('CountriesSeeder');

        $this->account = resolve(AccountRepositoryInterface::class);

        $this->profile = resolve(ProfileRepositoryInterface::class);

        $this->artist = resolve(ArtistRepositoryInterface::class);

        $this->catalogEntity = resolve(CatalogEntityRepositoryInterface::class);

        $this->user = resolve(UserRepositoryInterface::class);

        $this->album = resolve(AlbumRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function setRepository()
    {
        $this->repo = resolve(LabelRepositoryInterface::class);
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

        $label = $this->repo->create($profile->toArray());

        $this->assertInstanceOf($this->repo->class(), $label);
        $this->assertInstanceOf($this->profile->class(), $label->profile);
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_updatesResource()
    {
        $profile = factory($this->profile->class())->make(['country_code' => 'US'])->toArray();

        $label = $this->repo->create($profile);

        $this->repo->update($label->id, [
            'country_code' => 'CA',
        ]);

        $this->assertTrue(
            $this->repo->findById($label->id)->profile->country->code === 'CA'
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_returnsModelInstance()
    {
        $profile = factory($this->profile->class())->make();

        $label = $this->repo->create($profile->toArray());

        $updated = $this->repo->update($label->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function test_method_delete_deletesResource()
    {
        $profile = factory($this->profile->class())->make();

        $label = $this->repo->create($profile->toArray());

        factory($this->artist->class())->create([
            'label_id' => $label->id
        ]);

        $label->delete();

        try {
            $this->repo->findById($label->id);
        } catch(ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Ensure that a newly-created Label morphs to a CatalogableEntity.
     *
     * @return void
     */
    public function test_catalogable_withNewLabel_morphsToCatalogableEntity()
    {
        $label = $this->repo->create(
            factory($this->profile->class())->make()->toArray()
        );

        $user = $this->makeUser();

        $catalogEntity = factory($this->catalogEntity->class())->make([
            'user_id' => $user->id,
            'catalogable_id' => $label->id,
            'catalogable_type' => $this->repo->class()
        ]);

        $this->catalogEntity->create($catalogEntity->toArray());

        $profile = factory($this->profile->class())->make([
            'profilable_id' => $label->id,
            'profilable_type' => $this->repo->class()
        ]);

        $this->profile->create($profile->toArray());

        $this->assertInstanceOf(
            $this->catalogEntity->class(),
            $label->catalogable
        );

        $this->assertInstanceOf($this->catalogEntity->class(), $label->catalogable);
    }

    /**
     * Ensure that any a newly-created Label morphs to a Profile.
     *
     * @return void
     */
    public function test_profile_withNewLabel_morphsToProfile()
    {
        $profile = factory($this->profile->class())->make();

        $label = factory($this->repo->class())->make(
            $profile->toArray()
        );

        $label = $this->repo->create($label->toArray());

        $this->assertInstanceOf($this->profile->class(), $label->profile);
    }

    /**
     * Ensure that when an Artist is associated with a Label, the Label has
     * many Artists.
     *
     * @return void
     */
    public function test_artists_whenAssociatedWithLabel_labelHasManyArtists()
    {
        $label = $this->repo->create(
            factory($this->profile->class())->make()->toArray()
        );

        $artist = $this->artist->create(
            factory($this->profile->class())->make(['label_id' => $label->id])->toArray()
        );

        $this->assertInstanceOf($this->artist->class(), $artist->label->artists->first());
    }

    /**
     * Ensure that when an Album is associated with an Artist that is associated
     * with a Label, the Label has many Albums.
     *
     * @return void
     */
    public function test_albums_whenAssociatedWithLabelThroughArtist_labelHasManyAlbums()
    {
        $label = $this->repo->create(
            factory($this->profile->class())->make()->toArray()
        );

        $artist = $this->artist->create(
            factory($this->profile->class())->make(['label_id' => $label->id])->toArray()
        );

        $this->album->create(
            factory($this->album->class())->make(['artist_id' => $artist->id])->toArray()
        );

        $this->assertInstanceOf($this->album->class(), $artist->label->albums->first());
    }
}
