<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;

use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class ControllerTestCase extends TestCase
{
    use RefreshDatabase;
}
