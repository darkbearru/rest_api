<?php

namespace Abramenko\RestApi\Services;

use Firebase\JWT\JWT;

class TokenService extends Service
{
    const ACCESS_TOKEN_KEY  = 'JWT-Secrets-Key';
    const ACCESS_TOKEN_LIFETIME = "1 day";
    const REFRESH_TOKEN_KEY = 'JWT-Secrets-Refresh-Key';
    const REFRESH_TOKEN_LIFETIME = "30 day";

    public static function Generate(array $payload): array|bool
    {
        $payload['iat'] = strtotime("+" . self::ACCESS_TOKEN_LIFETIME);
        $jwtAccess = JWT::encode($payload, self::ACCESS_TOKEN_KEY, 'HS256');

        $payload['iat'] = strtotime("+" . self::REFRESH_TOKEN_LIFETIME);
        $jwtRefresh = JWT::encode($payload, self::REFRESH_TOKEN_KEY, 'HS256');

        return [
            "access"    => $jwtAccess,
            "refresh"   => $jwtRefresh
        ];
    }
    public static function Save(): bool
    {
        return true;
    }
}
