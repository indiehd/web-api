<?php

namespace Tests;

use Artisan;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use ReflectionClass;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected static $migrationsRun = false;

    public function setUp()
    {
        parent::setUp();

        if (!static::$migrationsRun) {
            Artisan::call('migrate:fresh');

            static::$migrationsRun = true;

            Artisan::call('db:seed', ['--class' => 'CountriesSeeder']);
            Artisan::call('db:seed', ['--class' => 'GenresSeeder']);
        }
    }

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
