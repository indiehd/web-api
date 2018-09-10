<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\UpdatePassword;
use App\Repositories\UserRepository;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PasswordController extends Controller
{
    /**
     * @var UserRepository
     */
    private $user;

    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }

    public function update(UpdatePassword $request, $id)
    {
        return $this->user->update($id, [
            'password' => bcrypt($request->password)
        ]);
    }
}
