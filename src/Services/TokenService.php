<?php

namespace Abramenko\RestApi\Services;

use Abramenko\RestApi\Models\TokenModel;
use Abramenko\RestApi\Models\UserModel;
use Exception;
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
                "+" . self::ACCESS_TOKEN_LIFETIME . " day"
            ),
            "refresh" => self::ghostGenerate(
                $payload,
                self::REFRESH_TOKEN_KEY,
                "+" . self::REFRESH_TOKEN_LIFETIME . " day"
            )
        ];
    }

    protected static function ghostGenerate(array $payload, string $key, string $expire): string
    {
        $payload = [
            'user' => $payload
        ];
        $payload['iat'] = strtotime("now");
        $payload['exp'] = strtotime($expire);
        return JWT::encode($payload, $key, 'HS256');
    }

    public static function ValidateAccessToken(string $token): array|object
    {
        // Проверяем accessToken на его влидность и срок жизни
        $tokenData = self::ValidateToken($token, self::ACCESS_TOKEN_KEY);
        if (!empty($tokenData)) {
            if (UserModel::IsUserExists('', $tokenData)) {
                // Пользовательсуществует, но надо проверить есть ли привязанные к нему refreshToken-ы
                // Иначе мы разлогились
                if (TokenModel::refreshTokenExists($tokenData['user']->id)) return $tokenData;
            }
        }

        // В случае проблем с accessToken, проверяем refreshToken
        if (!($token = self::ValidateRefreshToken(
            '',
            !empty($tokenData) ? $tokenData['user']->id : 0))
        ) return [];

        return [
            "access" => self::ghostGenerate(
                $token['user'],
                self::ACCESS_TOKEN_KEY,
                "+" . self::ACCESS_TOKEN_LIFETIME . " day"
            ),
            'user' => $token['user']
        ];
    }

    public static function ValidateToken(string $token, string $key): array|object
    {
        try {
            $decoded = (array)JWT::decode($token, new Key($key, 'HS256'));
        } catch (Exception $e) {
            return [];
        }
        if ($decoded['exp'] < strtotime('now')) return [];
        return $decoded;
    }

    public static function ValidateRefreshToken(string $refreshToken = '', int $userID = 0): array|object
    {
        if (!$refreshToken) {
            if (empty ($_COOKIE)) return [];
            if (empty ($_COOKIE['refreshToken'])) return [];
            $refreshToken = $_COOKIE['refreshToken'];
        }
        $token = self::ValidateToken($refreshToken, self::REFRESH_TOKEN_KEY);
        if (empty($token)) return [];
        // Access Token userID и данные refreshToken userID не совпадают
        // Отменяем доступ
        if ($token['user']->id != $userID) return [];

        // Если с refreshToken проблем нет, то проверяем на его соответствие с тем что хранится в базе
        $fromBase = TokenModel::validateToken($refreshToken);
        if (empty($fromBase)) {
            TokenModel::deleteToken($refreshToken);
            return [];
        }
        return [
            'id' => $fromBase['id'],
            'email' => $fromBase['email']
        ];
    }

    public static function getTokenFromHeader(): bool|string
    {
        $headers = getallheaders();
        if (empty ($headers['Authorization'])) return false;
        try {
            list ($keyword, $token) = explode(' ', ($headers['Authorization']));
        } catch (Exception $e) {
            $keyword = $token = '';
        }
        if ($keyword != 'Bearer' || empty($token)) return false;
        return $token;
    }

}
