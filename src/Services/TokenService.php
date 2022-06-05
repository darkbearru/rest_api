<?php

namespace Abramenko\RestApi\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class TokenService extends Service
{
    const ACCESS_TOKEN_KEY = 'JWT-ErT334s-Secrets2-eMo3-34Key';
    const ACCESS_TOKEN_LIFETIME = 1;
    const REFRESH_TOKEN_KEY = 'JWT-ErT334s-Secrets2-R4efrEsh-34Key';
    const REFRESH_TOKEN_LIFETIME = 30;

    public static function Generate(array $payload): array|bool
    {
        return [
            "access" => self::ghostGenerate(
                $payload,
                self::ACCESS_TOKEN_KEY,
                "+" . (string)self::ACCESS_TOKEN_LIFETIME . " day"
            ),
            "refresh" => self::ghostGenerate(
                $payload,
                self::REFRESH_TOKEN_KEY,
                "+" . (string)self::REFRESH_TOKEN_LIFETIME . " day"
            )
        ];
    }

    protected static function ghostGenerate(array $payload, string $key, string $expire): string
    {
        $payload['iat'] = strtotime("now");
        $payload['exp'] = strtotime($expire);
        return JWT::encode($payload, $key, 'HS256');
    }

    public static function Compare(string $token): bool
    {
        if (self::ghostCompare($token, self::ACCESS_TOKEN_KEY)) return true;
        if (empty ($_COOKIE['refreshToken'])) return false;
        if (self::ghostCompare($_COOKIE['refreshToken'], self::REFRESH_TOKEN_KEY)) return true;
        return false;
    }

    protected static function ghostCompare(string $token, string $key): bool
    {
        $decoded = (array)JWT::decode($token, new Key($key, 'HS256'));
        echo '<pre>';
        print_r($decoded);
        echo '</pre>';
        return true;


    }

    public static function Save(): bool
    {
        return true;
    }


}
