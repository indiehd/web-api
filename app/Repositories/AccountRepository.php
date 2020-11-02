<?php

namespace App\Repositories;

use App\Account;
use App\Contracts\AccountRepositoryInterface;

class AccountRepository extends CrudRepository implements AccountRepositoryInterface
{
    /**
     * @var string
     */
    protected $class = Account::class;

    /**
     * @var Account
     */
    protected $account;

    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    public function class()
    {
        return $this->class;
    }

    public function model()
    {
        return $this->account;
    }
}
