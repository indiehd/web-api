<?php

namespace App\Repositories;

use App\Account;
use App\Contracts\AccountRepositoryInterface;

class AccountRepository extends BaseRepository implements AccountRepositoryInterface
{
    /**
     * @var string $class
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

    public function all()
    {
        return $this->model()->all();
    }

    public function findById($id)
    {
        return $this->model()->find($id);
    }

    public function create(array $data)
    {
        return $this->model()->create($data);
    }
}
