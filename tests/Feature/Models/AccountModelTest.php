<?php

namespace Tests\Feature;

use Symfony\Component\VarDumper\Cloner\Data;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use DatabaseSeeder;

use App\Account;
use App\User;

class AccountModelTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Ensure that any random Account belongs to a User.
     */
    public function test_user_randomAccount_hasOneUser()
    {
        $this->assertInstanceOf(User::class, Account::inRandomOrder()->first()->user);
    }
}
