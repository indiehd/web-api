<?php

namespace Tests\Feature\Controllers;

use Artisan;

use Tests\TestCase;

use Illuminate\Foundation\Testing\DatabaseTransactions;

abstract class ControllerTestCase extends TestCase
{
    use DatabaseTransactions;

    protected static $staticSeedsRun = false;

    public function setUp()
    {
        parent::setUp();

        if (!static::$staticSeedsRun) {
            Artisan::call('db:seed', ['--class' => 'StaticDataSeeder']);
        }
    }
}
