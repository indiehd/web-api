<?php

namespace Tests\Feature\Models;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use DatabaseSeeder;

use App\Account;
use App\User;

class AccountModelTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    /**
     * Ensure that any random Account belongs to a User.
     *
     * @return void
     */
    public function test_user_randomAccount_belongsToUser()
    {
        $this->assertInstanceOf(User::class, Account::inRandomOrder()->first()->user);
    }
}
