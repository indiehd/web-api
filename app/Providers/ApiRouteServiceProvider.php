<?php

namespace App\Providers;

use App\Services\ApiRoute;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class ApiRouteServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Map all Api Routes here
     *
     */
    public function map()
    {
        $this->apiRoute('users', 'UserController')->addDefaultRoutes();
        $this->apiRoute('artists', 'ArtistController')->addDefaultRoutes();
        $this->apiRoute('albums', 'AlbumController')->addDefaultRoutes();
        $this->apiRoute('songs', 'SongController')->addDefaultRoutes();
        // ...
    }

    protected function apiRoute($prefix, $controller)
    {
        return new ApiRoute($prefix, $controller);
    }
}
