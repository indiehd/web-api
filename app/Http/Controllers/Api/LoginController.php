<?php

namespace App\Http\Controllers\Api;

use App\Contracts\UserRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\Request;

class LoginController extends Controller
{

    /**
     * @var UserRepositoryInterface
     */
    protected $user;

    /**
     * @var Hasher
     */
    protected $hasher;

    public function __construct(UserRepositoryInterface $user, Hasher $hasher)
   {
       $this->user = $user;
       $this->hasher = $hasher;
   }

    public function validateUser(Request $request)
   {
       $email = $request->get('email');
       $password = $request->get('password');

       $user = $this->user->model()
           ->whereEmail($email)
           ->first();

       if (!$user) {
           return response('User Not Found', 401);
       }

       if ($this->hasher->check($password, $user->password)) {
           $token = $user->token();

           if (!$token) {
               $token = $user->createToken('Personal Access Token')->accessToken;
           }

           return response([
               'access_token' => $token,
               'user' => $user
           ], 200);
       }

       return response('Unauthorized', 401);
   }
}
