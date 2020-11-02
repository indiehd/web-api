<?php

namespace App\Repositories;

use App\Contracts\AccountRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\User;
use Illuminate\Contracts\Hashing\Hasher;

class UserRepository extends CrudRepository implements UserRepositoryInterface
{
    /**
     * @var string
     */
    protected $class = User::class;

    /**
     * @var \App\User
     */
    private $user;

    /**
     * @var \App\Account
     */
    private $account;

    /**
     * @var Hasher
     */
    private $hasher;

    /**
     * UserRepository constructor.
     *
     * @param User $user
     * @param AccountRepositoryInterface $account
     * @param Hasher $hasher
     */
    public function __construct(
        User $user,
        AccountRepositoryInterface $account,
        Hasher $hasher
    ) {
        $this->user = $user;

        $this->account = $account;

        $this->hasher = $hasher;
    }

    public function class()
    {
        return $this->class;
    }

    public function model()
    {
        return $this->user;
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
            'email' => $data['email'],
            'name' => $data['name'],
            'password' => $this->hasher->make($data['password']),
        ]);

        $this->account->create(
            ['user_id' => $user->id] + $data['account']
        );

        return $user;
    }

    public function update($id, array $data)
    {
        $model = $this->findById($id);

        if (! empty($data['password'])) {
            $data['password'] = $this->hasher->make($data['password']);
        }

        $model->update($data);

        return $model;
    }

    protected function catalogable()
    {
        return $this->user->entity->catalogable ?: $this->user->entity->catalogable;
    }
}
