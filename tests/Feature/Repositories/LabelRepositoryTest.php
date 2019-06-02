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
     * @var ArtistRepositoryInterface $artist
     */
    protected $artist;

    /**
     * @var AlbumRepositoryInterface $album
     */
    protected $album;

    /**
     * @inheritdoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seed('CountriesSeeder');

        $this->account = resolve(AccountRepositoryInterface::class);

        $this->profile = resolve(ProfileRepositoryInterface::class);

        $this->catalogEntity = resolve(CatalogEntityRepositoryInterface::class);

        $this->user = resolve(UserRepositoryInterface::class);

        $this->artist = resolve(ArtistRepositoryInterface::class);

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
     * Ensure the method create() creates a new record in the database and creates a profile for
     * said Artist.
     *
     * @return void
     */
    public function testCreateStoresNewResource()
    {
        $profile = factory($this->profile->class())->make();

        $label = $this->repo->create($profile->toArray());

        $this->assertInstanceOf($this->repo->class(), $label);
        $this->assertInstanceOf($this->profile->class(), $label->profile);
    }

    /**
     * @inheritdoc
     */
    public function testUpdateUpdatesResource()
    {
        $profile = factory($this->profile->class())->raw(['country_code' => 'US']);

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
    public function testUpdateReturnsModelInstance()
    {
        $profile = factory($this->profile->class())->make();

        $label = $this->repo->create($profile->toArray());

        $updated = $this->repo->update($label->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function testDeleteDeletesResource()
    {
        $profile = factory($this->profile->class())->make();

        $label = $this->repo->create($profile->toArray());

        factory($this->artist->class())->create([
            'label_id' => $label->id
        ]);

        $label->delete();

        try {
            $this->repo->findById($label->id);
        } catch (ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Ensure that a Label morphs to a CatalogableEntity.
     *
     * @return void
     */
    public function testLabelMorphsToCatalogableEntity()
    {
        $label = $this->repo->create(
            factory($this->profile->class())->raw()
        );

        $user = $this->createUser();

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
     * Ensure that a Label morphs to a Profile.
     *
     * @return void
     */
    public function testLabelMorphsToProfile()
    {
        $profile = factory($this->profile->class())->make();

        $label = factory($this->repo->class())->make(
            $profile->toArray()
        );

        $label = $this->repo->create($label->toArray());

        $this->assertInstanceOf($this->profile->class(), $label->profile);
    }

    /**
     * Ensure that when an Artist is related to a Label, the Label has
     * many Artists.
     *
     * @return void
     */
    public function testWhenLabelRelatedToArtistItHasManyArtists()
    {
        $label = $this->repo->create(
            factory($this->profile->class())->raw()
        );

        $artist = $this->artist->create(
            factory($this->profile->class())->raw(['label_id' => $label->id])
        );

        $this->assertInstanceOf($this->artist->class(), $artist->label->artists->first());
    }

    /**
     * Ensure that when an Album is related to an Artist that is related to
     * a Label, the Label has many Albums.
     *
     * @return void
     */
    public function testWhenLabelRelatedToArtistItHasManyAlbums()
    {
        $label = $this->repo->create(
            factory($this->profile->class())->raw()
        );

        $artist = $this->artist->create(
            factory($this->profile->class())->raw(['label_id' => $label->id])
        );

        #$this->album->create(
            factory($this->album->class())->create(['artist_id' => $artist->id]);
        #);

        $this->assertInstanceOf($this->album->class(), $artist->label->albums->first());
    }

    /**
     * Create a User.
     *
     * @param array $userProperties
     * @param array $accountProperties
     * @return \App\User
     */
    protected function createUser(array $userProperties = [], array $accountProperties = [])
    {
        $user = factory($this->user->class())->make($userProperties);

        $account = factory($this->account->class())->make($accountProperties);

        $user = $this->user->create([
            'email' => $user->email,
            'name' => $user->name,
            'password' => $user->password,
            'account' => $account->toArray(),
        ]);

        return $user;
    }
}
