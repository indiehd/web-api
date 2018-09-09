<?php

namespace Tests\Feature\Repositories;

use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\LabelRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;

class LabelRepositoryTest extends RepositoryCrudTestCase
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

        $this->artist = resolve(ArtistRepositoryInterface::class);
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
        $profile = factory($this->profile->class())->make(['country_code' => 'US'])->toArray();

        $label = $this->repo->create($profile);

        $updated = $this->repo->update($label->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function test_method_delete_deletesResource()
    {
        $profile = factory($this->profile->class())->make(['country_code' => 'US'])->toArray();

        $label = $this->repo->create($profile);

        factory($this->artist->class())->create([
            'label_id' => $label->id
        ]);

        $label->delete();

        $this->assertNull($this->repo->findById($label->id));
    }
}