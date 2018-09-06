<?php

namespace App\Repositories;

use App\Contracts\UserRepositoryInterface;
use App\User;
use App\Account;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * @var string $class
     */
    protected $class = User::class;

    /**
     * @var \App\User
     */
    private $user;

    /**
     * @var \App\Account
     */
    public $account;

    public function __construct(
        User $user,
        Account $account
    )
    {
        $this->user = $user;

        $this->account = $account;
    }

    public function class()
    {
        return $this->class;
    }

    public function model()
    {
        return $this->user;
    }

    public function all()
    {
        return $this->user->all();
    }

    public function findById($id)
    {
        return $this->user->find($id);
    }

    public function songs()
    {
        return $this->catalogable() ? $this->catalogable()->songs : $this->purchasedSongs();
    }

    public function purchasedSongs()
    {
        return $this->user->purchased();
    }

    public function create(array $data)
    {
        $user = $this->user->create([
            'username' => $data['username'],
            'password' => $data['password'],
        ]);

        $this->account->create([
            'user_id' => $user->id,
            'email' => $data['email'],
        ]);

        return $user;
    }

    public function delete($id)
    {
        return $this->findById($id)->delete();
    }

    /*
     * Protected Methods
     * -----------------
     */

    protected function catalogable()
    {
        return $this->user->entity->catalogable ?: $this->user->entity->catalogable;
    }
}
