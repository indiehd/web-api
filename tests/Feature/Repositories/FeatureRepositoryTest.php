<?php

namespace Tests\Feature\Repositories;

use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\FeaturedRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FeatureRepositoryTest extends RepositoryCrudTestCase
{
    /**
     * @var ArtistRepositoryInterface
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
        $artist = $this->factory($this->artist)->create();

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
        $artist = $this->factory($this->artist)->create();

        $featured = $this->repo->create([
            'featurable_id' => $artist->id,
            'featurable_type' => $this->artist->class(),
        ]);

        $this->repo->update($featured->id, [
            'featurable_type' => 'App\Foo',
        ]);

        $this->assertTrue(
            $this->repo->findById($featured->id)->featurable_type === 'App\Foo'
        );
    }

    /**
     * @inheritdoc
     */
    public function testUpdateReturnsModelInstance()
    {
        $artist = $this->factory($this->artist)->create();

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
        $artist = $this->factory($this->artist)->create();

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
