<?php

namespace App\Http\Controllers\Api;

use App\Contracts\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // todo: create api routes

    /**
     * @var UserRepositoryInterface
     */
    private $user;

    public function __construct(UserRepositoryInterface $user)
    {
        $this->user = $user;
    }

    public function all()
    {
        return $this->user->all();
    }

    public function getById($id)
    {
        return $this->user->findById($id);
    }

    public function create(Request $request)
    {
        return $this->user->create($request->all());
    }

    public function update(Request $request, $id)
    {
        return $this->user->update($id, $request->all());
    }

    public function delete($id)
    {
        return $this->user->delete($id);
    }
}