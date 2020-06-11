<?php

namespace Tests\Feature\Controllers;

use App\Contracts\UserRepositoryInterface;
use Tests\TestCase;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;

abstract class ControllerTestCase extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $user_repository = resolve(UserRepositoryInterface::class);
        $user = factory($user_repository->class())->create();

        Passport::actingAs($user);
    }
}
