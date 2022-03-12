<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('user.auth')->except(['register', 'login']);
    }

    public function register(): JsonResponse
    {
        $user = request(['username', 'password']);
        return User::getModel()->signUpByUsernamePwd($user['username'], $user['password']);
    }

    public function login(): JsonResponse
    {
        $user = request(['username', 'password']);
        return User::getModel()->signInByUsernamePwd($user['username'], $user['password']);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function me(): JsonResponse
    {
        $user = request()->get('user');
        if (!isset($user)) {
            return response()->json([
                'msg' => '用户不存在',
            ], 400);
        }

        return response()->json([
            'me' => $user,
        ]);
    }
}
