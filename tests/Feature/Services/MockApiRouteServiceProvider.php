<?php

namespace Tests\Feature\Services;

use App\Providers\RouteServiceProvider;
use App\Services\ApiRoute;

class MockApiRouteServiceProvider extends RouteServiceProvider
{
    public function boot()
    {
        parent::boot();
    }

    public function map()
    {
        $this->apiRoute('test', 'TestController')
            ->addDefaults()
            ->add('/whatever', 'whatever')
            ->add('/another', 'another', 'post');

        $this->apiRoute('except', 'ExceptController')
            ->except(['update']);

        $this->apiRoute('only', 'OnlyController')
            ->only(['index']);
    }

    public function apiRoute($prefix, $controller)
    {
        return new ApiRoute($prefix, $controller);
    }
}
