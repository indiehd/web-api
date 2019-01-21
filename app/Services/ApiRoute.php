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

    /**
     * @var array $routes
     */
    private $routes = [
        'index' => [
            'uri' => '/',
            'httpMethod' => 'get',
            'action' => 'all'
        ],
        'show' => [
            'uri' => '/{id}',
            'httpMethod' => 'get',
            'action' => 'show'
        ],
        'store' => [
            'uri' => '/create',
            'httpMethod' => 'post',
            'action' => 'store'
        ],
        'update' => [
            'uri' => '/{id}',
            'httpMethod' => 'put',
            'action' => 'update'
        ],
        'destroy' => [
            'uri' => '/{id}',
            'httpMethod' => 'delete',
            'action' => 'destroy'
        ],
    ];

    public function __construct($prefix, $controller)
    {
        $this->prefix = $prefix;
        $this->controller = $controller;
    }

    public function addDefaultRoutes()
    {
        $controller = $this->controller;
        $prefix = $this->prefix;
        $routes = $this->routes;

        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(function () use ($routes, $controller, $prefix) {
                Route::prefix($prefix)->group(function () use ($routes, $controller, $prefix) {
                    foreach ($routes as $key => $route) {
                        $uri = $route['uri'];
                        $httpMethod = $route['httpMethod'];
                        $action = $route['action'];

                        Route::$httpMethod($uri, "$controller@$action")->name("$prefix.$key");
                    }
                });
            });

        return $this;
    }

    public function except(array $routes = [])
    {
        $this->routes = array_except($this->routes, $routes);
        return $this;
    }

    public function addRoute($uri, $controllerMethod, $httpMethod = 'get', $name = null)
    {
        $controller = $this->controller;
        $prefix = $this->prefix;
        $httpMethod = strtolower($httpMethod);
        $name = is_null($name) ? strtolower($controllerMethod) : strtolower($name);

        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(function () use (
                $controller,
                $prefix,
                $name,
                $uri,
                $controllerMethod,
                $httpMethod
            ) {
                Route::prefix($this->prefix)->group(function () use (
                    $controller,
                    $prefix,
                    $name,
                    $uri,
                    $controllerMethod,
                    $httpMethod
                ) {
                    Route::$httpMethod($uri, "$controller@$controllerMethod")
                        ->name("$prefix." . snake_case($controllerMethod));
                });
            });

        return $this;
    }
}
