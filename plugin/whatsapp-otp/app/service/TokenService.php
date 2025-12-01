<?php
declare(strict_types=1);

namespace plugin\whatsappOtp\app\service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class TokenService
{
    public static function issueTokens(int $userId): array
    {
        $c = config('plugin.whatsapp-otp.jwt');
        $now = time();

        $access = [
            'sub' => $userId,
            'type' => 'access',
            'iat' => $now,
            'exp' => $now + ($c['access_exp'] ?? 3600),
        ];
        $refresh = [
            'sub' => $userId,
            'type' => 'refresh',
            'iat' => $now,
            'exp' => $now + ($c['refresh_exp'] ?? 86400 * 30),
        ];

        $accessToken = JWT::encode($access, (string)$c['access_secret'], 'HS256');
        $refreshToken = JWT::encode($refresh, (string)$c['refresh_secret'], 'HS256');

        return ['access_token' => $accessToken, 'refresh_token' => $refreshToken];
    }

    public static function verifyAccessToken(string $token)
    {
        $c = config('plugin.whatsapp-otp.jwt');
        try {
            return JWT::decode($token, new Key((string)$c['access_secret'], 'HS256'));
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function verifyRefreshToken(string $token)
    {
        $c = config('plugin.whatsapp-otp.jwt');
        try {
            return JWT::decode($token, new Key((string)$c['refresh_secret'], 'HS256'));
        } catch (\Throwable $e) {
            return null;
        }
    }
}
