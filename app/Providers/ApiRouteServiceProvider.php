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
     * Map all API Routes here.
     *
     */
    public function map()
    {
        $this->apiRoute('users', 'UserController')->addDefaultRoutes();
        $this->apiRoute('artists', 'ArtistController')->addDefaultRoutes();
        $this->apiRoute('albums', 'AlbumController')->addDefaultRoutes();
        $this->apiRoute('labels', 'LabelController')->addDefaultRoutes();
        $this->apiRoute('songs', 'SongController')->addDefaultRoutes();
        $this->apiRoute('genres', 'GenreController')->addDefaultRoutes();

        $this->apiRoute('featured', 'FeaturedController')
            ->except(['store', 'update'])
            ->addDefaultRoutes();

        $this->apiRoute('orders', 'OrderController')
            ->except(['store', 'update'])
            ->addDefaultRoutes()
            ->addRoute('/storeOrder', 'storeOrder', 'post')
            ->addRoute('/addItems/{orderId}', 'addItems', 'post')
            ->addRoute('/removeItems/{orderId}', 'removeItems', 'delete');
    }

    protected function apiRoute($prefix, $controller)
    {
        return new ApiRoute($prefix, $controller);
    }
}
