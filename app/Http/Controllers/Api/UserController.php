<?php

namespace App\Http\Controllers\Api;

use App\Contracts\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserCollection;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Factory as ValidatorInterface;

class UserController extends Controller
{
    /**
     * @var UserRepositoryInterface
     */
    private $user;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(UserRepositoryInterface $user, ValidatorInterface $validator)
    {
        $this->user = $user;
        $this->validator = $validator;
    }

    public function all()
    {
        return response(new UserCollection($this->user->all()), 200);
    }

    public function getById($id)
    {
        return response($this->user->findById($id), 200);
    }

    public function create(Request $request)
    {
        $this->check($request->all(), [
            'email' => 'required|unique:users',
            'password' => 'required',
        ]);

        return response($this->user->create($request->all()), 200);
    }

    public function update(Request $request, $id)
    {
        return response($this->user->update($id, $request->all()), 200);
    }

    public function delete($id)
    {
        return response($this->user->delete($id), 200);
    }

    /*
     * ------------------
     * Protected Methods
     * ------------------
     */

    protected function check(array $data, array $rules)
    {
        $validation = $this->validator->make($data, $rules);

        if($validation->fails()) {
            return response($validation->errors()->toArray(), 400);
        }

        return true;
    }
}