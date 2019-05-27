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
     * Map all Api Routes here.
     *
     */
    public function map()
    {
        $this->apiRoute('auth', 'LoginController')
            ->addRoute('login', 'validateUser', 'post', 'login');

        $this->apiRoute('users', 'UserController')->addDefaults();
        $this->apiRoute('artists', 'ArtistController')->addDefaults();
        $this->apiRoute('albums', 'AlbumController')->addDefaults();
        $this->apiRoute('songs', 'SongController')->addDefaults();

        $this->apiRoute('featured', 'FeaturedController')
            ->add('artists', 'artists', 'get', 'artists');

        $this->apiRoute('orders', 'OrderController')
            ->except(['store', 'update'])
            ->add('/storeOrder', 'storeOrder', 'post')
            ->add('/addItems/{orderId}', 'addItems', 'post')
            ->add('/removeItems/{orderId}', 'removeItems', 'delete');
    }

    protected function apiRoute($prefix, $controller)
    {
        return new ApiRoute($prefix, $controller);
    }
}
