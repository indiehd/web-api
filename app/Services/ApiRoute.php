<?php

namespace App\Services;

use Illuminate\Support\Facades\Route;

class ApiRoute
{
    /**
     * @var string $namespace
     */
    protected $namespace = 'App\Http\Controllers\Api';

    /**
     * @var $prefix
     */
    private $prefix;

    /**
     * @var $controller
     */
    private $controller;


    public function __construct($prefix, $controller)
    {
        $this->prefix = $prefix;
        $this->controller = $controller;
    }

    public function mapDefaultRoutes()
    {
        $controller = $this->controller;
        $prefix = $this->prefix;

        Route::prefix('api')
            ->middleware('-')
            ->namespace($this->namespace)
            ->group(function () use ($controller, $prefix) {
                Route::prefix($this->prefix)->group(function () use ($controller, $prefix) {
                    Route::get('/', "$controller@all")->name("$prefix.index");
                    Route::get('/{id}', "$controller@show")->name("$prefix.show");
                    Route::post('/create', "$controller@store")->name("$prefix.store");
                    Route::put('/{id}', "$controller@update")->name("$prefix.update");
                    Route::delete('/{id}', "$controller@destroy")->name("$prefix.destroy");
                });
            });

        return $this;
    }

    public function mapAdditionalRoute($uri, $controllerMethod, $httpMethod = 'get')
    {
        $httpMethod = strtolower($httpMethod);
        $controllerMethod = snake_case($controllerMethod);

        $controller = $this->controller;
        $prefix = $this->prefix;

        Route::$httpMethod($uri, "$controller@$controllerMethod")->name("$prefix.$controllerMethod");

        return $this;
    }
}
