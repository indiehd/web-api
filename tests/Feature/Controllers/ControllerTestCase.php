<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;

use Illuminate\Foundation\Testing\DatabaseTransactions;

abstract class ControllerTestCase extends TestCase
{
    use DatabaseTransactions;
}
