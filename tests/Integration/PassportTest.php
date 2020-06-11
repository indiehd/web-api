<?php

namespace Tests\Integration;

use App\Contracts\AlbumRepositoryInterface;
use App\Contracts\ArtistRepositoryInterface;
use App\Contracts\ProfileRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\User;
use CountriesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;
use Tests\TestCase;

class PassportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var AlbumRepositoryInterface $album
     */
    protected $album;

    /**
     * @var ArtistRepositoryInterface $artist
     */
    protected $artist;

    /**
     * @var ProfileRepositoryInterface $profile
     */
    protected $profile;

    /**
     * @var UserRepositoryInterface $user
     */
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(CountriesSeeder::class);

        $this->album = resolve(AlbumRepositoryInterface::class);
        $this->artist = resolve(ArtistRepositoryInterface::class);
        $this->profile = resolve(ProfileRepositoryInterface::class);
        $this->user = resolve(UserRepositoryInterface::class);

        Artisan::call('passport:install');
        $rc = Artisan::call('passport:client', [
            '--name' => 'test',
            '--public' => true,
            '--user_id' => 1,
            '--redirect_uri' => 'http://localhost/callback',
        ]);

        $this->assertEquals(0, $rc);
    }

    /** @var test */
    public function test_unauthorized_access()
    {

        /** @var Router $router */
        $router = resolve(Router::class);
        $router->addRoute('GET', '/protected', function () {
            return 'Ok';
        })->middleware(['api', 'auth:api']);
        $response = $this->json('GET', '/protected');
        $response->dump();
        $response->assertStatus(401);
    }

    /** @var test */
    public function test_authorized_access()
    {
        /** @var Router $router */
        $router = resolve(Router::class);
        $router->addRoute('GET', '/protected', function () {
            return 'Ok';
        })->middleware(['api', 'auth:api']);

        Passport::actingAs(factory(User::class)->create());

        $response = $this->json('GET', '/protected');
        $response->assertStatus(200);
    }

    protected function createArtist()
    {
        $artist = factory($this->artist->class())->create();

        factory($this->profile->class())->create(
            [
                'profilable_id' => $artist->id,
                'profilable_type' => $this->artist->class()
            ]
        );

        return $artist;
    }

    protected function createAlbum()
    {
        return factory($this->album->class())->create([
            'is_active' => true,
            'has_explicit_lyrics' => false,
        ]);
    }
}
