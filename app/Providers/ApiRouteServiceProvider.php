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
     * Map all API Routes here.
     */
    public function map()
    {
        $this->apiRoute('users', 'UserController')->addDefaults();
        $this->apiRoute('artists', 'ArtistController')->addDefaults();
        $this->apiRoute('albums', 'AlbumController')->addDefaults();
        $this->apiRoute('labels', 'LabelController')->addDefaults();
        $this->apiRoute('songs', 'SongController')->addDefaults();
        $this->apiRoute('genres', 'GenreController')->addDefaults();

        $this->apiRoute('featured', 'FeaturedController')
            ->add('artists', 'artists', 'get', 'artists');

        // TODO Commented-out until tests are updated for "new" Order implementation.

        /*
        $this->apiRoute('orders', 'OrderController')
            ->except(['store', 'update'])
            ->add('/storeOrder', 'storeOrder', 'post')
            ->add('/addItems/{orderId}', 'addItems', 'post')
            ->add('/removeItems/{orderId}', 'removeItems', 'delete');
        */
    }

    protected function apiRoute($prefix, $controller)
    {
        return new ApiRoute($prefix, $controller);
    }
}
