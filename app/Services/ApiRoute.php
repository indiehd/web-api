<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class ApiRoute
{
    /**
     * @var string
     */
    protected $namespace = 'App\Http\Controllers\Api';

    /**
     * @var
     */
    private $prefix;

    /**
     * @var
     */
    private $controller;

    /**
     * @var array
     */
    private $routes = [
        'index' => [
            'uri' => '/',
            'httpMethod' => 'get',
            'action' => 'index',
        ],
        'show' => [
            'uri' => '/{id}',
            'httpMethod' => 'get',
            'action' => 'show',
        ],
        'store' => [
            'uri' => '/create',
            'httpMethod' => 'post',
            'action' => 'store',
        ],
        'update' => [
            'uri' => '/{id}',
            'httpMethod' => 'put',
            'action' => 'update',
        ],
        'destroy' => [
            'uri' => '/{id}',
            'httpMethod' => 'delete',
            'action' => 'destroy',
        ],
    ];

    public function __construct($prefix, $controller)
    {
        $this->prefix = $prefix;
        $this->controller = $controller;
    }

    protected function defaults()
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

    public function addDefaults(array $excepts = [])
    {
        if (count($excepts) > 0) {
            return $this->except($excepts);
        }

        return $this->defaults();
    }

    public function except(array $routes)
    {
        $this->routes = Arr::except($this->routes, $routes);

        return $this->defaults();
    }

    public function only(array $routes)
    {
        $this->routes = Arr::only($this->routes, $routes);

        return $this->defaults();
    }

    public function add($uri, $controllerMethod, $httpMethod = 'get', $name = null)
    {
        $controller = $this->controller;
        $prefix = $this->prefix;
        $httpMethod = strtolower($httpMethod);
        $name = is_null($name) ? Str::snake($controllerMethod) : $name;

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
                    Route::$httpMethod($uri, "$controller@$controllerMethod")->name("$prefix.$name");
                });
            });

        return $this;
    }
}
