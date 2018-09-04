<?php

namespace Tests\Feature\Controllers;

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
            $this->seed('StaticDataSeeder');
        }
    }
}
