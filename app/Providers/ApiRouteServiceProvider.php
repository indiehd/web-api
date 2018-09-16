<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class ApiRouteServiceProvider extends ServiceProvider
{
    protected $namespace = 'App\Http\Controllers\Api';

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
        $this->mapRoutes('users', 'UserController');
        $this->mapRoutes('artists', 'ArtistController');
        $this->mapRoutes('albums', 'AlbumController');
        $this->mapRoutes('flacfiles', 'FlacFileController');
        $this->mapRoutes('skus', 'SkuController');
        $this->mapRoutes('songs', 'SongController');
        // ...
    }

    private function mapRoutes($prefix, $controller)
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(function () use ($controller, $prefix) {
                Route::prefix($prefix)->group(function () use ($controller, $prefix) {
                    Route::get('/', "$controller@all")->name("$prefix.index");
                    Route::get('/{id}', "$controller@show")->name("$prefix.show");
                    Route::post('/create', "$controller@store")->name("$prefix.store");
                    Route::put('/{id}', "$controller@update")->name("$prefix.update");
                    Route::delete('/{id}', "$controller@destroy")->name("$prefix.destroy");
                });
            });
    }
}
