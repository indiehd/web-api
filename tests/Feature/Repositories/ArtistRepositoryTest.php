<?php

namespace Tests\Feature\Repositories;

use App\Contracts\AccountRepositoryInterface;
use App\Contracts\AlbumRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\CatalogEntityRepositoryInterface;
use App\Contracts\LabelRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\SongRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use IndieHD\Velkart\Database\Seeders\CountriesSeeder;

class ArtistRepositoryTest extends RepositoryCrudTestCase
{
    /**
     * @var AccountRepositoryInterface
     */
    protected $account;

    /**
     * @var ProfileRepositoryInterface
     */
    protected $profile;

    /**
     * @var AlbumRepositoryInterface
     */
    protected $album;

    /**
     * @var LabelRepositoryInterface
     */
    protected $label;

    /**
     * @var UserRepositoryInterface
     */
    protected $user;

    /**
     * @var CatalogEntityRepositoryInterface
     */
    protected $catalogEntity;

    /**
     * @var SongRepositoryInterface
     */
    protected $song;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(CountriesSeeder::class);

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
     * Ensure the method create() creates a new record in the database and creates a profile for
     * said Artist.
     *
     * @return void
     */
    public function testCreateStoresNewResource()
    {
        $profile = $this->factory($this->profile)->make();

        $artist = $this->repo->create($profile->toArray());

        $this->assertInstanceOf($this->repo->class(), $artist);
        $this->assertInstanceOf($this->profile->class(), $artist->profile);
    }

    /**
     * @inheritdoc
     */
    public function testUpdateUpdatesResource()
    {
        $profile = $this->factory($this->profile)->make(['country_code' => 'US']);

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
    public function testUpdateReturnsModelInstance()
    {
        $profile = $this->factory($this->profile)->make();

        $artist = $this->repo->create($profile->toArray());

        $updated = $this->repo->update($artist->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function testDeleteDeletesResource()
    {
        $profile = $this->factory($this->profile)->make();

        $artist = $this->repo->create($profile->toArray());

        $artist->delete();

        try {
            $this->repo->findById($artist->id);
        } catch (ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Ensure that an Artist morphs to a CatalogableEntity.
     *
     * @return void
     */
    public function testArtistMorphsToCatalogableEntity()
    {
        $artist = $this->repo->create(
            $this->factory($this->profile)->raw()
        );

        $user = $this->createUser();

        $catalogEntity = $this->factory($this->catalogEntity)->make([
            'user_id' => $user->id,
            'catalogable_id' => $artist->id,
            'catalogable_type' => $this->repo->class(),
        ]);

        $this->catalogEntity->create($catalogEntity->toArray());

        $profile = $this->factory($this->profile)->make([
            'profilable_id' => $artist->id,
            'profilable_type' => $this->repo->class(),
        ]);

        $this->profile->create($profile->toArray());

        $this->assertInstanceOf(
            $this->catalogEntity->class(),
            $artist->catalogable
        );
    }

    /**
     * Ensure that an Artist morphs to a Profile.
     *
     * @return void
     */
    public function testArtistMorphsToProfile()
    {
        $profile = $this->factory($this->profile)->make();

        $artist = $this->factory()->make(
            $profile->toArray()
        );

        $artist = $this->repo->create($artist->toArray());

        $this->assertInstanceOf(
            $this->profile->class(),
            $artist->profile
        );
    }

    /**
     * Verify that when an Artist is related to a Label, the Artist belongs
     * to the Label.
     *
     * @return void
     */
    public function testWhenArtistRelatedToLabelItBelongsToLabel()
    {
        $labelProfile = $this->factory($this->profile)->make();

        $label = $this->label->create($labelProfile->toArray());

        $artistProfile = $this->factory($this->profile)->make();

        $artist = $this->factory()->make(
            array_merge($artistProfile->toArray(), ['label_id' => $label->id])
        );

        $artist = $this->repo->create($artist->toArray());

        $this->assertInstanceOf($this->label->class(), $artist->label);
    }

    /**
     * Verify that when an Album is related to an Artist, the Artist has
     * at least one Album.
     *
     * @return void
     */
    public function testWhenArtistRelatedToAlbumItHasManyAlbums()
    {
        $artist = $this->repo->create(
            $this->factory($this->profile)->raw()
        );

        $this->factory($this->album)->create(['artist_id' => $artist->id]);

        $this->assertInstanceOf($this->album->class(), $artist->albums()->first());
    }

    /**
     * Verify that when an Album is related to an Artist, the Artist has
     * at least one Song (through an Album).
     *
     * @return void
     */
    public function testWhenArtistRelatedToAlbumItHasManySongs()
    {
        $artist = $this->repo->create(
            $this->factory($this->profile)->raw()
        );

        $this->factory($this->album)->create(['artist_id' => $artist->id]);

        $this->assertInstanceOf($this->song->class(), $artist->albums()->first()->songs->first());
    }

    /**
     * Create a new User.
     *
     * @param array $userProperties
     * @param array $accountProperties
     * @return \App\User
     */
    protected function createUser(array $userProperties = [], array $accountProperties = [])
    {
        $user = $this->factory($this->user)->make($userProperties);

        $account = $this->factory($this->account)->make($accountProperties);

        $user = $this->user->create([
            'email' => $user->email,
            'name' => $user->name,
            'password' => $user->password,
            'account' => $account->toArray(),
        ]);

        return $user;
    }
}
