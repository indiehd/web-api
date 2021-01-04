<?php

namespace Tests\Feature\Repositories;

use App\Contracts\AccountRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\CatalogEntityRepositoryInterface;
use App\Contracts\LabelRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use IndieHD\Velkart\Database\Seeders\CountriesSeeder;

class ProfileRepositoryTest extends RepositoryCrudTestCase
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
     * @var ArtistRepositoryInterface
     */
    protected $artist;

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
     * @inheritdoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->seed(CountriesSeeder::class);

        $this->account = resolve(AccountRepositoryInterface::class);

        $this->profile = resolve(ProfileRepositoryInterface::class);

        $this->artist = resolve(ArtistRepositoryInterface::class);

        $this->label = resolve(LabelRepositoryInterface::class);

        $this->user = resolve(UserRepositoryInterface::class);

        $this->catalogEntity = resolve(CatalogEntityRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function setRepository()
    {
        $this->repo = resolve(ProfileRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function testCreateStoresNewResource()
    {
        $profile = $this->makeProfile();

        $this->assertInstanceOf(
            $this->repo->class(),
            $this->repo->create($profile->toArray())
        );
    }

    /**
     * @inheritdoc
     */
    public function testUpdateUpdatesResource()
    {
        $profile = $this->repo->create($this->makeProfile()->toArray());

        $newValue = 'Foobius Barius';

        $property = 'moniker';

        $profile = $this->repo->update($profile->id, [
            $property => $newValue,
        ]);

        $this->assertTrue(
            $profile->fresh()->{$property} === $newValue
        );
    }

    /**
     * @inheritdoc
     */
    public function testUpdateReturnsModelInstance()
    {
        $profile = $this->repo->create($this->makeProfile()->toArray());

        $updated = $this->repo->update($profile->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function testDeleteDeletesResource()
    {
        $profile = $this->repo->create($this->makeProfile()->toArray());

        $profile->delete();

        try {
            $this->repo->findById($profile->id);
        } catch (ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Ensure that models of every Profilable type in the database morph
     * to a Profile.
     *
     * @return void
     */
    public function testAllProfilableTypesMorphToProfile()
    {
        $user = $this->createUser();

        $artist = $this->artist->create(
            $this->factory()->raw()
        );

        $this->factory($this->catalogEntity)->create([
            'user_id' => $user->id,
            'catalogable_id' => $artist->id,
            'catalogable_type' => $this->artist->class(),
        ]);

        $this->factory($this->profile)->create([
            'profilable_id' => $artist->id,
            'profilable_type' => $this->artist->class(),
        ]);

        $this->assertInstanceOf($this->profile->class(), $artist->profile);

        $user = $this->createUser();

        $label = $this->label->create(
            $this->factory()->raw()
        );

        $this->factory($this->catalogEntity)->create([
            'user_id' => $user->id,
            'catalogable_id' => $label->id,
            'catalogable_type' => $this->label->class(),
        ]);

        $this->factory($this->profile)->create([
            'profilable_id' => $label->id,
            'profilable_type' => $this->label->class(),
        ]);

        $this->assertInstanceOf($this->profile->class(), $label->profile);
    }

    /**
     * Create a User.
     *
     * @return \App\User
     */
    protected function createUser()
    {
        $user = $this->factory($this->user)->make();

        $user = $this->user->create([
            'email' => $user->email,
            'name' => $user->name,
            'password' => $user->password,
            'account' => $this->factory($this->account)->raw(),
        ]);

        return $user;
    }

    /**
     * Make a Profile.
     *
     * @return \App\Profile
     */
    protected function makeProfile()
    {
        $artist = $this->artist->create(
            $this->factory()->raw()
        );

        $profile = $this->factory()->make([
            'profilable_id' => $artist->id,
            'profilable_type' => $this->repo->class(),
        ]);

        return $profile;
    }
}
