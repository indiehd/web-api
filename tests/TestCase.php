<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use ReflectionClass;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function isInstantiable($class)
    {
        try {
            $class = new ReflectionClass($class);
            return $class->isInstantiable();
        } catch(\ReflectionException $e) {
            throw new $e;
        }
    }
}
