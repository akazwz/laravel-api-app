<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Utils\JwtUtils;
use Closure;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\Pure;

class UserAuth
{
    private JwtUtils $jwtUtils;

    #[Pure] public function __construct()
    {
        $this->jwtUtils = new JwtUtils();
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $token = $request->header('token');
        /* 没有 token */
        if (!isset($token)) {
            return response()->json([
                'msg' => '暂无 token',
            ], 401);
        }

        $decodedArray = $this->jwtUtils->parseToken($token);
        /* token 不合法 */
        if (!isset($decodedArray['iss']) || !isset($decodedArray['exp']) || !isset($decodedArray['uid'])) {
            return response()->json([
                'msg' => 'token 不合法',
            ], 401);
        }

        if ($decodedArray['exp'] <= time()) {
            return response()->json([
                'msg' => 'token 已过期',
            ], 401);
        }

        $uid = $decodedArray['uid'];

        // 判断用户是否存在
        $user = User::where(['uid' => $uid])->first();
        if (is_null($user)) {
            return response()->json([
                'msg' => '用户不存在',
            ], 401);
        }

        $request->attributes->add(['user' => $user]);

        return $next($request);
    }
}
