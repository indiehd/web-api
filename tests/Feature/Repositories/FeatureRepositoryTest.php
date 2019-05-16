<?php

namespace Tests\Feature\Repositories;

use App\Contracts\FeaturedRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FeatureRepositoryTest extends RepositoryCrudTestCase
{
    /**
     * @var $artist ArtistRepositoryInterface
     */
    protected $artist;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed('CountriesSeeder');

        $this->artist = resolve(ArtistRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function setRepository()
    {
        $this->repo = resolve(FeaturedRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function testCreateStoresNewResource()
    {
        $artist = factory($this->artist->class())->create();

        $featured = $this->repo->create([
            'featurable_id' => $artist->id,
            'featurable_type' => $this->artist->class(),
        ]);

        $this->assertInstanceOf($this->repo->class(), $featured);
    }

    /**
     * @inheritdoc
     */
    public function testUpdateUpdatesResource()
    {
        $artist = factory($this->artist->class())->create();

        $featured = $this->repo->create([
            'featurable_id' => $artist->id,
            'featurable_type' => $this->artist->class(),
        ]);

        $this->repo->update($featured->id, [
            'is_active' => false,
        ]);

        $this->assertTrue(
            $this->repo->findById($featured->id)->is_active === 0
        );
    }

    /**
     * @inheritdoc
     */
    public function testUpdateReturnsModelInstance()
    {
        $artist = factory($this->artist->class())->create();

        $featured = $this->repo->create([
            'featurable_id' => $artist->id,
            'featurable_type' => $this->artist->class(),
        ]);

        $updated = $this->repo->update($featured->id, []);

        $this->assertInstanceOf($this->repo->class(), $updated);
    }

    /**
     * @inheritdoc
     */
    public function testDeleteDeletesResource()
    {
        $artist = factory($this->artist->class())->create();

        $featured = $this->repo->create([
            'featurable_id' => $artist->id,
            'featurable_type' => $this->artist->class(),
        ]);

        $featured->delete();

        try {
            $this->repo->findById($artist->id);
        } catch (ModelNotFoundException $e) {
            $this->assertTrue(true);
        }
    }
}
