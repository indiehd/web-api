<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UpdatePassword;
use App\Repositories\UserRepository;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\Request;

class PasswordController extends Controller
{
    /**
     * @var UserRepository
     */
    private $user;

    /**
     * @var Hasher
     */
    private $hasher;

    public function __construct(UserRepository $user, Hasher $hasher)
    {
        $this->user = $user;
        $this->hasher = $hasher;
    }

    public function update(UpdatePassword $request, $id)
    {
        return $this->user->update($id, [
            'password' => $this->hasher->make($request->password)
        ]);
    }
}
