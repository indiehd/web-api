<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use IndieHD\Velkart\Database\Seeders\CountriesSeeder;
use ReflectionClass;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function setUp(): void
    {
        parent::setUp();

        #$this->seed(CountriesSeeder::class);
    }

    protected function isInstantiable($class)
    {
        try {
            $class = new ReflectionClass($class);

            return $class->isInstantiable();
        } catch (\ReflectionException $e) {
            throw new $e;
        }
    }
}
