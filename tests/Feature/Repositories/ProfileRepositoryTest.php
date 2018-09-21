<?php

namespace Tests\Feature\Repositories;

use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\LabelRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Contracts\CatalogEntityRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProfileRepositoryTest extends RepositoryCrudTestCase
{
    /**
     * @var $profile ProfileRepositoryInterface
     */
    protected $profile;

    /**
     * @var $artist ArtistRepositoryInterface
     */
    protected $artist;

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
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        $this->seed('CountriesSeeder');

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
     * Make a new Profile object.
     *
     * @param array $properties
     * @return \App\Profile
     *
     */
    public function make(array $properties = [])
    {
        $this->setRepository();

        return factory($this->repo->class())->make($properties);
    }

    /**
     * @inheritdoc
     */
    public function test_method_create_storesNewResource()
    {
        $artist = factory($this->artist->class())->create();

        $profile = factory($this->repo->class())->make([
            'profilable_id' => $artist->id,
            'profilable_type' => $this->repo->class(),
        ])->toArray();

        $this->assertInstanceOf(
            $this->repo->class(),
            $this->repo->create($profile)
        );
    }

    /**
     * @inheritdoc
     */
    public function test_method_update_updatesResource()
    {
        $artist = factory($this->artist->class())->create();

        $profile = factory($this->repo->class())->create([
            'profilable_id' => $artist->id,
            'profilable_type' => $this->repo->class(),
        ]);

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
        $artist = factory($this->artist->class())->create();

        $profile = factory($this->repo->class())->create([
            'profilable_id' => $artist->id,
            'profilable_type' => $this->repo->class(),
        ]);

        $updated = $this->repo->update($profile->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function test_method_delete_deletesResource()
    {
        $artist = factory($this->artist->class())->create();

        $profile = factory($this->repo->class())->create([
            'profilable_id' => $artist->id,
            'profilable_type' => $this->repo->class(),
        ]);

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
        $artist = factory($this->artist->class())->create();

        factory($this->catalogEntity->class())->create([
            'user_id' => factory($this->user->class())->create()->id,
            'catalogable_id' => $artist->id,
            'catalogable_type' => $this->artist->class()
        ]);

        factory($this->profile->class())->create([
            'profilable_id' => $artist->id,
            'profilable_type' => $this->artist->class()
        ]);

        $this->assertInstanceOf($this->profile->class(), $artist->profile);

        $label = factory($this->label->class())->create();

        factory($this->catalogEntity->class())->create([
            'user_id' => factory($this->user->class())->create()->id,
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
