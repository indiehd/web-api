<?php

namespace Tests\Feature\Repositories;

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
     * @var  $profile  ProfileRepositoryInterface
     */
    protected $profile;

    /**
     * @var $catalogEntity CatalogEntityRepositoryInterface
     */
    protected $catalogEntity;

    /**
     * @var $user UserRepositoryInterface
     */
    protected $user;

    /**
     * @var $album AlbumRepositoryInterface
     */
    protected $album;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        $this->seed('CountriesSeeder');

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
        $label = factory($this->repo->class())->create();

        factory($this->catalogEntity->class())->create([
            'user_id' => factory($this->user->class())->create()->id,
            'catalogable_id' => $label->id,
            'catalogable_type' => $this->repo->class()
        ]);

        factory($this->profile->class())->create([
            'profilable_id' => $label->id,
            'profilable_type' => $this->repo->class()
        ]);

        $this->assertInstanceOf($this->catalogEntity->class(), $label->catalogable);
    }

    /**
     * Ensure that any a newly-created Label morphs to a Profile.
     *
     * @return void
     */
    public function test_profile_withNewLabel_morphsToProfile()
    {
        $label = factory($this->repo->class())->create();

        factory($this->catalogEntity->class())->create([
            'user_id' => factory($this->user->class())->create()->id,
            'catalogable_id' => $label->id,
            'catalogable_type' => $this->repo->class()
        ]);

        factory($this->profile->class())->create([
            'profilable_id' => $label->id,
            'profilable_type' => $this->repo->class()
        ]);

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
        $artist = factory($this->artist->class())->state('onLabel')->create();

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
        $artist = factory($this->artist->class())->state('onLabel')->create();

        factory($this->album->class())->create(['artist_id' => $artist->id]);

        $this->assertInstanceOf($this->album->class(), $artist->label->albums->first());
    }
}
