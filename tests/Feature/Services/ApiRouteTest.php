<?php

namespace Tests\Feature;

use App\Providers\RouteServiceProvider;
use App\Services\ApiRoute;
use Illuminate\Routing\Router;
use Tests\TestCase;

class ApiRouteTest extends TestCase
{

    protected $methods = [
        'index' => 'get',
        'show' => 'get',
        'store' => 'post',
        'update' => 'put',
        'destroy' => 'delete',

        'whatever' => 'get',
        'another' => 'post'
    ];

    /**
     * @var ApiRoute $service
     */
    protected $service;

    /**
     * @var Router $router
     */
    protected $router;


    public function setUp(): void
    {
        parent::setUp();

        $this->service = new MockApiRouteServiceProvider($this->app);
        $this->service->boot();

        $this->router = resolve(Router::class);

    }

    public function test_router_can_be_constructed()
    {
        $this->assertInstanceOf(Router::class, $this->router);
    }

    public function test_it_created_routes()
    {
        $methods = array_keys($this->methods);

        foreach ($methods as $method) {
            $this->assertNotNull(
                $this->router
                    ->getRoutes()
                    ->getByName("tests.$method")
            );
        }
    }

    public function testExceptMethodHasIndexRoute()
    {
        $this->assertNotNull(
            $this->router
                ->getRoutes()
                ->getByName("excepts.index")
        );
    }

    public function testExceptMethodExcludesUpdateRoute()
    {
        $this->assertNull(
            $this->router
                ->getRoutes()
                ->getByName("excepts.update")
        );
    }

    public function test_routes_have_proper_httpMethods()
    {
        $methods = $this->methods;

        foreach ($methods as $method => $httpMethod) {
            $this->assertTrue(
                $this->has_httpMethod("tests.$method", $httpMethod),
                "Invalid http method for $method! Expected $httpMethod"
            );
        }
    }

    public function test_routes_have_api_prefix()
    {
        $methods = array_keys($this->methods);

        foreach ($methods as $method) {
            $this->assertTrue($this->has_prefix("tests.$method"));
        }
    }

    public function test_routes_have_api_middleware()
    {
        $methods = array_keys($this->methods);

        foreach ($methods as $method) {
            $this->assertTrue($this->has_middleware("tests.$method"));
        }
    }

    public function test_routes_have_api_namespace()
    {
        $methods = array_keys($this->methods);

        foreach ($methods as $method) {
            $this->assertTrue($this->has_namespace("tests.$method"));
        }
    }

    protected function has_httpMethod($route, $httpMethod)
    {
        $httpMethod = strtoupper($httpMethod);

        return in_array($httpMethod,
            $this->router
                ->getRoutes()
                ->getByName($route)
                ->methods()
        );
    }

    protected function has_prefix($route, $prefix = 'api')
    {
        return starts_with(
            $this->router
                ->getRoutes()
                ->getByName($route)
                ->uri(),
            $prefix
        );
    }

    protected function has_middleware($route, $middleware = 'api')
    {
        return in_array($middleware,
            $this->router
                ->getRoutes()
                ->getByName($route)
                ->middleware()
        );
    }

    protected function has_namespace($route, $namespace = 'App\Http\Controllers\Api')
    {
        return (
            $namespace === $this->router
                ->getRoutes()
                ->getByName($route)->action['namespace']
        );
    }

}

class MockApiRouteServiceProvider extends RouteServiceProvider
{
    public function boot()
    {
        parent::boot();
    }

    public function map()
    {
        $this->apiRoute('tests', 'TestController')
            ->addDefaultRoutes()
            ->addRoute('/whatever', 'whatever')
            ->addRoute('/another', 'another', 'post');

        $this->apiRoute('excepts', 'ExceptsController')
            ->except(['update'])
            ->addDefaultRoutes();
    }

    public function apiRoute($prefix, $controller)
    {
        return new ApiRoute($prefix, $controller);
    }
}
