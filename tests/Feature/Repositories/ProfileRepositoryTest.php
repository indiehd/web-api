<?php

namespace Tests\Feature\Repositories;

use App\Contracts\AccountRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\LabelRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\CatalogEntityRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProfileRepositoryTest extends RepositoryCrudTestCase
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
     * @var ArtistRepositoryInterface $artist
     */
    protected $artist;

    /**
     * @var LabelRepositoryInterface $label
     */
    protected $label;

    /**
     * @var UserRepositoryInterface $user
     */
    protected $user;

    /**
     * @var CatalogEntityRepositoryInterface $catalogEntity
     */
    protected $catalogEntity;

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
     * Create a new User object.
     *
     * @return \App\User
     */
    public function createUser()
    {
        $user = factory($this->user->class())->make();

        $user = $this->user->create([
            'email' => $user->email,
            'password' => $user->password,
            'account' => factory($this->account->class())->raw()
        ]);

        return $user;
    }

    /**
     * Make a new Profile object.
     *
     * @return \App\Profile
     */
    public function makeProfile()
    {
        $artist = $this->artist->create(
            factory($this->repo->class())->raw()
        );

        $profile = factory($this->repo->class())->make([
            'profilable_id' => $artist->id,
            'profilable_type' => $this->repo->class(),
        ]);

        return $profile;
    }

    /**
     * @inheritdoc
     */
    public function test_method_create_storesNewResource()
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
    public function test_method_update_updatesResource()
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
    public function test_method_update_returnsModelInstance()
    {
        $profile = $this->repo->create($this->makeProfile()->toArray());

        $updated = $this->repo->update($profile->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function test_method_delete_deletesResource()
    {
        $profile = $this->repo->create($this->makeProfile()->toArray());

        $profile->delete();

        try {
            $this->repo->findById($profile->id);
        } catch(ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Ensure that models of every Profilable type in the database morph
     * to a Profile.
     *
     * @return void
     */
    public function test_profilable_allDistinctTypes_morphToProfile()
    {
        $user = $this->createUser();

        $artist = $this->artist->create(
            factory($this->repo->class())->raw()
        );

        factory($this->catalogEntity->class())->create([
            'user_id' => $user->id,
            'catalogable_id' => $artist->id,
            'catalogable_type' => $this->artist->class()
        ]);

        factory($this->profile->class())->create([
            'profilable_id' => $artist->id,
            'profilable_type' => $this->artist->class()
        ]);

        $this->assertInstanceOf($this->profile->class(), $artist->profile);

        $user = $this->createUser();

        $label = $this->label->create(
            factory($this->repo->class())->raw()
        );

        factory($this->catalogEntity->class())->create([
            'user_id' => $user->id,
            'catalogable_id' => $label->id,
            'catalogable_type' => $this->label->class()
        ]);

        factory($this->profile->class())->create([
            'profilable_id' => $label->id,
            'profilable_type' => $this->label->class()
        ]);

        $this->assertInstanceOf($this->profile->class(), $label->profile);
    }
}
