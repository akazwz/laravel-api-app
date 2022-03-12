<?php

namespace App\Utils;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtUtils
{
    public function generateTokenByUid(string $uid): string
    {
        $key = env('JWT_SECRET');
        $payload = [
            'iss' => 'zwz',
            'exp' => time() + 3600,
            'uid' => $uid,
        ];
        return JWT::encode($payload, $key, 'HS256');
    }

    /**
     * @param string $token
     * @return array ['iss', 'exp', 'uid']
     */
    public function parseToken(string $token): array
    {
        $key = env('JWT_SECRET');
        JWT::$leeway = 60;
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        return (array)$decoded;
    }
}
